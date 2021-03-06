<?php namespace net\xp_lang\tests\integration;

use xp\compiler\emit\php\V54Emitter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\task\CompilationTask;
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
    $this->emitter= new V54Emitter();
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
      new FileSource($this->getClass()->getPackage()->getPackage('src')->getResourceAsStream($resource)),
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
    unset($this->emitter, $this->files);
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
