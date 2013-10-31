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

  #[@test]
  public function compileA_which_requires_B_which_requires_A() {
    $this->compileSource('A.xp')->name();
  }

  #[@test]
  public function compileB_which_requires_A() {
    $this->compileSource('B.xp')->name();
  }

  #[@test]
  public function compileC_which_requires_B_and_A() {
    $this->compileSource('C.xp')->name();
  }
}
