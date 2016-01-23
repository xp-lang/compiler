<?php namespace xp\compiler;

use io\File;
use io\Folder;
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\ExtensionEqualsFilter;
use io\collections\iterate\AnyOfFilter;
use util\Properties;
use lang\ResourceProvider;
use lang\reflect\Package;
use xp\compiler\diagnostic\DefaultDiagnosticListener;
use xp\compiler\diagnostic\QuietDiagnosticListener;
use xp\compiler\diagnostic\VerboseDiagnosticListener;
use xp\compiler\io\FileSource;
use xp\compiler\io\CommandLineSource;
use xp\compiler\io\FileManager;
use util\log\Logger;
use util\log\LogCategory;
use util\log\LogLevel;
use util\log\ConsoleAppender;
use util\cmd\Console;
use lang\ClassLoader;

/**
 * Compiles and optimizes PHP and XP language source code
 * ========================================================================
 *
 * - Compile a single file, writing output to **Test.class.php**:
 *   ```sh
 *   $ xp compile Test.xp
 *   ```
 * - Compile all files in the given folders, write output to **target/**:
 *   ```sh
 *   $ xp compile -o target src/main/xp src/test/xp
 *   ```
 * - Fast compilation, ignores missing apidocs, no optimization
 *   ```sh
 *   $ xp compile -p rad Test.php
 *   ```
 * - Emit sourcecode compatible with PHP 5.4 (default is *PHP 5.5*):
 *   ```sh
 *   $ xp compile -E php5.4 Test.xp
 *   ```
 *
 * The option `-v` will show verbose diagnostics, `-q` will suppress all
 * output except errors.
 *
 * Supports PHP [*.php*] and XP [*.xp*] syntax.
 */
class CompileRunner extends \lang\Object {
  
  static function __static() {
    ResourceProvider::getInstance();      // Register res:// protocol
  }

  /**
   * Displays usage
   *
   * @return  int exitcode
   */
  protected static function usage() {
    Console::$err->writeLine('XP Compiler: `xp compile [sources]`. xp help compile has the details!');
    return 1;
  }

  /**
   * Returns file targets from a folder
   *
   * @param   string uri
   * @param   bool recursive
   * @return  xp.compiler.io.FileSource[]
   */
  protected static function fromFolder($uri, $recursive) {
    static $filter= null;

    if (null === $filter) {
      $filter= new AnyOfFilter();
      foreach (Syntax::available() as $ext => $syntax) {
        $filter->add(new ExtensionEqualsFilter($ext));
      }
    }
    
    $files= [];
    $it= new FilteredIOCollectionIterator(new FileCollection($uri), $filter, $recursive);
    foreach ($it as $element) {
      $files[]= new FileSource(new File($element->getURI()));
    }
    return $files;
  } 

  /**
   * Creates and returns a file manager which publically exposed compiled
   * types via the "declared" member, indexed by their name.
   *
   * @return  xp.compiler.io.FileManager
   */
  protected static function declaringFileManager() {
    return newinstance('xp.compiler.io.FileManager', [], '{
      public $declared= [];
      public function write($r, \io\File $target) {
        $r->executeWith([]);
        $this->declared[]= \lang\XPClass::forName($r->type()->name());
      }
    }');
  }

  /**
   * Entry point method
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    if (empty($args)) {
      return self::usage();
    }

    foreach (ClassLoader::getLoaders() as $loader) {
      if ($loader instanceof JitClassLoader) ClassLoader::removeLoader($loader);
    }

    // Set up compiler
    $compiler= new Compiler();
    $manager= new FileManager();
    $manager->setSourcePaths(\xp::$classpath);
    
    // Handle arguments
    $profiles= ['default'];
    $emitter= 'php5.5';
    $result= function($success) { return $success ? 0 : 1; };
    $files= [];
    $listener= new DefaultDiagnosticListener(Console::$out);
    for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
      if ('-?' === $args[$i] || '--help' === $args[$i]) {
        return self::usage();
      } else if ('-cp' === $args[$i]) {
        \lang\ClassLoader::registerPath($args[++$i]);
      } else if ('-sp' === $args[$i]) {
        $manager->addSourcePath($args[++$i]);
      } else if ('-v' === $args[$i]) {
        $listener= new VerboseDiagnosticListener(Console::$out);
      } else if ('-q' === $args[$i]) {
        $listener= new QuietDiagnosticListener(Console::$out);
      } else if ('-t' === $args[$i]) {
        $levels= LogLevel::NONE;
        foreach (explode(',', $args[++$i]) as $level) {
          $levels |= LogLevel::named($level);
        }
        $compiler->setTrace(create(new LogCategory('xcc'))->withAppender(new ConsoleAppender(), $levels));
      } else if ('-E' === $args[$i]) {
        $emitter= $args[++$i];
      } else if ('-p' === $args[$i]) {
        $profiles= explode(',', $args[++$i]);
      } else if ('-o' === $args[$i]) {
        $output= $args[++$i];
        $folder= new Folder($output);
        $folder->exists() || $folder->create();
        $manager->setOutput($folder);
      } else if ('-N' === $args[$i]) {
        $dir= $args[++$i];
        $manager->addSourcePath($dir);
        $files= array_merge($files, self::fromFolder($dir, false));
      } else if (is_dir($args[$i])) {
        $dir= $args[$i];
        $manager->addSourcePath($dir);
        $files= array_merge($files, self::fromFolder($dir, true));
      } else {
        $files[]= new FileSource(new File($args[$i]));
      }
    }
    
    // Check
    if (empty($files)) {
      Console::$err->writeLine('*** No files given (-? will show usage)');
      return 2;
    }
    
    // Setup emitter
    sscanf($emitter, '%[^0-9]%d.%d', $language, $major, $minor);
    try {
      $emit= \lang\XPClass::forName('xp.compiler.emit.Emitter')->cast(Package::forName('xp.compiler.emit')
        ->getPackage($language)
        ->loadClass(($major ? ('V'.$major.$minor) : '').'Emitter')
        ->newInstance()
      );
    } catch (\lang\ClassCastException $e) {
      Console::$err->writeLine('*** Not an emitter implementation: ', $e->compoundMessage());
      return 4;
    } catch (\lang\IllegalAccessException $e) {
      Console::$err->writeLine('*** Cannot use emitter named "', $emitter, '": ', $e->compoundMessage());
      return 4;
    } catch (\lang\Throwable $e) {
      Console::$err->writeLine('*** No emitter named "', $emitter, '": ', $e->compoundMessage());
      return 4;
    }

    // Load compiler profile configurations
    try {
      $reader= new CompilationProfileReader();
      foreach ($profiles as $configuration) {
        $reader->addSource(new Properties('res://xp/compiler/'.$configuration.'.xcp.ini'));
      }
      $emit->setProfile($reader->getProfile());
    } catch (\lang\Throwable $e) {
      Console::$err->writeLine('*** Cannot load profile configuration(s) '.implode(',', $profiles).': '.$e->getMessage());
      return 3;
    }
    
    // Compile files and pass return value to result handler
    return $result($compiler->compile($files, $listener, $manager, $emit), array_slice($args, $i + 1));
  }
}
