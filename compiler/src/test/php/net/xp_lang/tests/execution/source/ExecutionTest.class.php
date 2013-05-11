<?php namespace net\xp_lang\tests\execution\source;

use xp\compiler\emit\source\Emitter;
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
  protected static $syntax;
  
  protected $emitter;
  protected $counter= 0;

  /**
   * Sets up compiler API
   *
   */
  #[@beforeClass]
  public static function setupCompilerApi() {
    self::$syntax= Syntax::forName('xp');
  }
  
  /**
   * Adds a check
   *
   * @param   xp.compiler.checks.Checks c
   * @param   bool error
   */
  protected function check(Check $c, $error= false) {
    $this->emitter->addCheck($c, $error);
  }

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->emitter= new Emitter();
    $this->counter= 0;
  }
  
  /**
   * Run statements and return result
   *
   * @param   string src
   * @param   string[] imports
   * @return  var
   */
  protected function run($src, array $imports= array()) {
    return $this->define(
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
    return $this->define(
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
  protected function define($type, $class, $parent, $src, array $imports= array()) {
    $class= 'Source'.$class;
    $scope= new TaskScope(new CompilationTask(
      new FileSource(new File(__FILE__), self::$syntax),
      new NullDiagnosticListener(),
      new FileManager(),
      $this->emitter
    ));
    
    // Parent class
    if ($parent instanceof XPClass) {
      $extends= $this->getClass()->getPackage()->getName().'.'.$parent->getName();
      $scope->addResolved($extends, new TypeReflection($parent));
      $scope->addTypeImport($extends);
    } else {
      $extends= $parent;
    }
    
    // Emit
    $r= $this->emitter->emit(
      self::$syntax->parse(new MemoryInputStream(
        implode("\n", $imports).
        ' public '.$type.' '.$class.' '.($extends ? ' extends '.$extends : '').$src
      ), $this->name), 
      $scope
    );
    \xp::gc();

    // DEBUG $r->writeTo(\util\cmd\Console::$out->getStream());
    $r->executeWith(array());
    return XPClass::forName($r->type()->name());
  }
}
