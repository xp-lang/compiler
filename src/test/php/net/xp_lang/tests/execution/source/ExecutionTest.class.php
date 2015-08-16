<?php namespace net\xp_lang\tests\execution\source;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\task\CompilationTask;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\types\TypeReflection;
use xp\compiler\types\TaskScope;
use xp\compiler\checks\Check;
use xp\compiler\Syntax;
use lang\XPClass;
use io\File;
use io\streams\MemoryInputStream;

/**
 * TestCase
 *
 */
abstract class ExecutionTest extends \unittest\TestCase {
  private static $emitter= null;
  protected $counter= 0;

  /**
   * Gets emitter
   *
   * @return   xp.compiler.emit.V53Emitter
   */
  protected static function emitter() {
    return self::$emitter ?: self::$emitter= new V53Emitter();
  }

  /**
   * Adds a check
   *
   * @param   xp.compiler.checks.Checks c
   * @param   bool error
   */
  protected static function check(Check $c, $error= false) {
    self::emitter()->addCheck($c, $error);
  }

  /**
   * Tears down compiler API
   */
  #[@afterClass]
  public static function removeChecks() {
    self::emitter()->clearChecks();
  }
  
  /**
   * Run statements and return result
   *
   * @param   string src
   * @param   string[] imports
   * @return  var
   */
  protected function run($src, array $imports= array()) {
    return self::define(
      'class', 
      ucfirst($this->name).'·'.($this->counter++), 
      null,
      '{ public var $member; public void run() { '.$src.' }}',
      $imports
    )->newInstance()->run();
  }

  /**
   * Compile statements and return type
   *
   * @param   string src
   * @param   string[] imports
   * @return  lang.XPClass
   */
  protected function compile($src, array $imports= array()) {
    return self::define(
      'class', 
      ucfirst($this->name).'·'.($this->counter++), 
      null,
      '{ public var $member; public void run() { '.$src.' }}',
      $imports
    );
  }
  
  /**
   * Define class from a given name and source
   *
   * @param   string type
   * @param   string class
   * @param   var parent either a string or a lang.XPClass
   * @param   string src
   * @param   string[] imports
   * @return  lang.XPClass
   */
  protected static function define($type, $class, $parent, $src, array $imports= array()) {
    $emitter= self::emitter();
    $emitter->clearMessages();
    $syntax= Syntax::forName('xp');
    $class= 'Source'.$class;
    $scope= new TaskScope(new CompilationTask(
      new FileSource(new File(__FILE__), $syntax),
      new NullDiagnosticListener(),
      new FileManager(),
      $emitter
    ));
    
    // Parent class
    if ($parent instanceof XPClass) {
      $extends= (new XPClass(__CLASS__))->getPackage()->getName().'.'.$parent->getName();
      $scope->addResolved($extends, new TypeReflection($parent));
      $scope->addTypeImport($extends);
    } else {
      $extends= $parent;
    }
    
    // Emit
    $r= $emitter->emit(
      $syntax->parse(new MemoryInputStream(
        implode("\n", $imports).
        ' public '.$type.' '.$class.' '.($extends ? ' extends '.$extends : '').$src
      ), $class), 
      $scope
    );
    \xp::gc();

    // DEBUG $r->writeTo(\util\cmd\Console::$out->getStream());
    $r->executeWith(array());
    return XPClass::forName($r->type()->name());
  }
}
