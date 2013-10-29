<?php namespace net\xp_lang\tests\integration;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\task\CompilationTask;
use xp\compiler\task\FileArgument;
use xp\compiler\Syntax;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.task.CompilationTask
 */
class CircularDependencyTest extends \unittest\TestCase {
  protected $emitter= null;
  protected $files= null;
  
  /**
   * Sets up test case
   */
  public function setUp() {
    $this->emitter= new V53Emitter();
    $this->files= new FileManager();
    $this->files->addSourcePath(dirname(__FILE__).'/src');    // FIXME: ClassPathManager?
    $this->files->setOutput(new \io\Folder(\lang\System::tempDir()));
  }
  
  /**
   * Compile source
   *
   * @param   string resource
   * @return  xp.compiler.types.Types
   * @throws  xp.compiler.CompilationException
   */
  protected function compileSource($resource) {
    $task= new CompilationTask(
      new FileArgument($this->getClass()->getPackage()->getPackage('src')->getResourceAsStream($resource)),
      new NullDiagnosticListener(),
      $this->files,
      $this->emitter
    );
    return $task->run();
  }
  
  /**
   * Tears down 
   */
  public function tearDown() {
    delete($this->emitter);
    delete($this->files);
  }
  
  /**
   * Test class A which requires class B which requires class A
   */
  #[@test]
  public function compileA() {
    $this->compileSource('A.xp')->name();
  }

  /**
   * Test class B which requires class A
   */
  #[@test]
  public function compileB() {
    $this->compileSource('B.xp')->name();
  }

  /**
   * Test class C which requires class B and class A
   */
  #[@test]
  public function compileC() {
    $this->compileSource('C.xp')->name();
  }
}
