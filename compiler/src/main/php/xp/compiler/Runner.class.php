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
use xp\compiler\emit\source\Emitter;
use xp\compiler\diagnostic\DefaultDiagnosticListener;
use xp\compiler\diagnostic\VerboseDiagnosticListener;
use xp\compiler\io\FileSource;
use xp\compiler\io\FileManager;
use util\log\Logger;
use util\log\LogCategory;
use util\log\LogLevel;
use util\log\ConsoleAppender;
use util\cmd\Console;

/**
 * XP Compiler, version {{VERSION}}
 * Copyright (c) 2008-2013 the XP group
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
 *   <li>-cp [path]: 
 *     Add path to classpath
 *   </li>
 *   <li>-sp [path]: 
 *     Adds path to source path (source path will equal classpath initially)
 *   </li>
 *   <li>-e [emitter]: 
 *     Use emitter, defaults to "source"
 *   </li>
 *   <li>-p [profile[,profile[,...]]]:
 *     Use compiler profiles (defaults to ["default"]) - xp/compiler/{profile}.xcp.ini
 *   </li>
 *   <li>-o [outputdir]: 
 *     Write compiled files to outputdir (will be created if not existant)
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
      array('#<pre>#', '#</pre>#', '#<li>#'),
      array(self::$line, self::$line, '* '),
      trim($markup)
    ));
  }

  /**
   * Shows usage
   *
   */
  protected static function showUsage() {
    $class= new \lang\XPClass(__CLASS__);
    Console::$err->writeLine(strtr(self::textOf($class->getComment()), array(
      '{{VERSION}}' => $class->getClassLoader()->getResource('VERSION')
    )));
    
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
    
    $files= array();
    $it= new FilteredIOCollectionIterator(new FileCollection($uri), $filter, $recursive);
    foreach ($it as $element) {
      $files[]= new FileSource(new File($element->getURI()));
    }
    return $files;
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
    
    $compiler= new Compiler();
    $manager= new FileManager();
    $manager->setSourcePaths(\xp::$classpath);
    $profiles= array('default');
    $emitter= 'source';
    
    // Handle arguments
    $files= array();
    $listener= new DefaultDiagnosticListener(Console::$out);
    for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
      if ('-?' == $args[$i] || '--help' == $args[$i]) {
        self::showUsage();
        return 2;
      } else if ('-cp' == $args[$i]) {
        ClassLoader::registerPath($args[++$i]);
      } else if ('-sp' == $args[$i]) {
        $manager->addSourcePath($args[++$i]);
      } else if ('-v' == $args[$i]) {
        $listener= new VerboseDiagnosticListener(Console::$out);
      } else if ('-t' == $args[$i]) {
        $levels= LogLevel::NONE;
        foreach (explode(',', $args[++$i]) as $level) {
          $levels |= LogLevel::named($level);
        }
        $compiler->setTrace(create(new LogCategory('xcc'))->withAppender(new ConsoleAppender(), $levels));
      } else if ('-e' == $args[$i]) {
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
      } else {
        $files[]= new FileSource(new File($args[$i]));
      }
    }
    
    // Check
    if (empty($files)) {
      Console::$err->writeLine('*** No files given (-? will show usage)');
      return 2;
    }
    
    // Setup emitter and load compiler profile configurations
    $emitter= Package::forName('xp.compiler.emit')->getPackage($emitter)->loadClass('Emitter')->newInstance();
    try {
      $reader= new CompilationProfileReader();
      foreach ($profiles as $configuration) {
        $reader->addSource(new Properties('res://xp/compiler/'.$configuration.'.xcp.ini'));
      }
      $emitter->setProfile($reader->getProfile());
    } catch (\lang\Throwable $e) {
      Console::$err->writeLine('*** Cannot load profile configuration(s) '.implode(',', $profiles).': '.$e->getMessage());
      return 3;
    }
    
    // Compile files. Use 0 exitcode to indicate success, 1 for failure
    return $compiler->compile($files, $listener, $manager, $emitter) ? 0 : 1;
  }
}
