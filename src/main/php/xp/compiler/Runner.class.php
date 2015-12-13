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
use xp\compiler\emit\php\V54Emitter;
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
 * XP Compiler, version {{VERSION}}
 * Copyright (c) 2008-2015 the XP group
 *
 * Usage:
 * <pre>
 * $ xcc [options] [path [path [... ]]]
 * </pre>
 *
 * Options is one of:
 * <ul>
 *   <li>-v:
 *     Display verbose diagnostics
 *   </li>
 *   <li>-q:
 *     Only display compilation errors
 *   </li>
 *   <li>-cp [path]: 
 *     Add path to classpath
 *   </li>
 *   <li>-sp [path]: 
 *     Adds path to source path (source path will equal classpath initially)
 *   </li>
 *   <li>-E [emitter]: 
 *     Use emitter, defaults to "php5.5"
 *   </li>
 *   <li>-p [profile[,profile[,...]]]:
 *     Use compiler profiles (defaults to ["default"]) - xp/compiler/{profile}.xcp.ini
 *   </li>
 *   <li>-o [outputdir]: 
 *     Write compiled files to outputdir (will be created if not existant)
 *   </li>
 *   <li>-e [language] [code] [arg[,arg[,arg]]] 
 *     Compile and run the given code, passing args as $args
 *   </li>
 *   <li>-w [language] [code] [arg[,arg[,arg]]] 
 *     Same as -e, but enclose in Console::writeLine()
 *   </li>
 *   <li>-t [level[,level[...]]]:
 *     Set trace level (all, none, info, warn, error, debug)
 *   </li>
 * </ul>
 * Path may be:
 * <ul>
 *   <li>[file.ext]:
 *     This file will be compiled
 *   </li>
 *   <li>[folder]:
 *     All files in this folder with all supported syntaxes will be compiled
 *   </li>
 *   <li>-N [folder]:
 *     Same as above, but not performed recursively
 *   </li>
 * </ul>
 */
class Runner extends \lang\Object {
  protected static $line;
  
  static function __static() {
    self::$line= str_repeat('=', 72);
    ResourceProvider::getInstance();      // Register res:// protocol
  }

  /**
   * Converts api-doc "markup" to plain text w/ ASCII "art"
   *
   * @param   string markup
   * @return  string text
   */
  protected static function textOf($markup) {
    return strip_tags(preg_replace(
      ['#<pre>#', '#</pre>#', '#<li>#'],
      [self::$line, self::$line, '* '],
      trim($markup)
    ));
  }

  /**
   * Shows usage
   *
   */
  protected static function showUsage() {
    $class= new \lang\XPClass(__CLASS__);
    Console::$err->writeLine(strtr(self::textOf($class->getComment()), [
      '{{VERSION}}' => $class->getClassLoader()->getResource('VERSION')
    ]));
    
    // List supported syntaxes
    Console::$err->writeLine(self::$line);
    Console::$err->writeLine('Syntax support:');
    foreach (Syntax::available() as $ext => $syntax) {
      Console::$err->writeLinef('  * [%-5s] %s', $ext, $syntax->getClass()->getComment());
    }
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
      public $declared= array();
      public function write($r, \io\File $target) {
        $r->executeWith(array());
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
      self::showUsage();
      return 2;
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
      if ('-?' == $args[$i] || '--help' == $args[$i]) {
        self::showUsage();
        return 2;
      } else if ('-cp' == $args[$i]) {
        \lang\ClassLoader::registerPath($args[++$i]);
      } else if ('-sp' == $args[$i]) {
        $manager->addSourcePath($args[++$i]);
      } else if ('-v' == $args[$i]) {
        $listener= new VerboseDiagnosticListener(Console::$out);
      } else if ('-q' == $args[$i]) {
        $listener= new QuietDiagnosticListener(Console::$out);
      } else if ('-t' == $args[$i]) {
        $levels= LogLevel::NONE;
        foreach (explode(',', $args[++$i]) as $level) {
          $levels |= LogLevel::named($level);
        }
        $compiler->setTrace(create(new LogCategory('xcc'))->withAppender(new ConsoleAppender(), $levels));
      } else if ('-E' == $args[$i]) {
        $emitter= $args[++$i];
      } else if ('-p' == $args[$i]) {
        $profiles= explode(',', $args[++$i]);
      } else if ('-o' == $args[$i]) {
        $output= $args[++$i];
        $folder= new Folder($output);
        $folder->exists() || $folder->create();
        $manager->setOutput($folder);
      } else if ('-N' == $args[$i]) {
        $files= array_merge($files, self::fromFolder($args[++$i], false));
      } else if (is_dir($args[$i])) {
        $files= array_merge($files, self::fromFolder($args[$i], true));
      } else if ('-e' == $args[$i]) {
        $listener= new QuietDiagnosticListener(Console::$out);
        $files[]= new CommandLineSource($args[++$i], $args[++$i], false);
        $manager= self::declaringFileManager();
        $result= function($success, $argv) use($manager) {
          if (!$success) return 1;
          return (int)$manager->declared[0]->getMethod('main')->invoke(null, [$argv]);
        };
        break;
      } else if ('-w' == $args[$i]) {
        $listener= new QuietDiagnosticListener(Console::$out);
        $files[]= new CommandLineSource($args[++$i], $args[++$i], true);
        $manager= self::declaringFileManager();
        $result= function($success, $argv) use($manager) {
          if (!$success) return 1;
          Console::writeLine($manager->declared[0]->getMethod('main')->invoke(null, [$argv]));
          return 0;
        };
        break;
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
