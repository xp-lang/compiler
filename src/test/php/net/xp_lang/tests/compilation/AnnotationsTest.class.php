<?php namespace net\xp_lang\tests\compilation;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\types\TypeName;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\task\CompilationTask;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\Syntax;
use io\File;
use io\streams\MemoryInputStream;

/**
 * TestCase
 *
 */
abstract class AnnotationsTest extends \unittest\TestCase {
  protected $scope;
  protected $emitter;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->emitter= new V53Emitter();
    $this->scope= new TaskScope(new CompilationTask(
      new FileSource(new File(__FILE__), Syntax::forName('xp')),
      new NullDiagnosticListener(),
      new FileManager(),
      $this->emitter
    ));
  }

  /**
   * Compile class from source and return compiled type
   *
   * @param   string src
   * @return  xp.compiler.types.Types
   */
  protected function compile($src) {
    $unique= 'FixtureClassFor'.$this->getClass()->getSimpleName().ucfirst($this->name);
    $r= $this->emitter->emit(
      Syntax::forName('xp')->parse(new MemoryInputStream(sprintf($src, $unique))),
      $this->scope
    );
    $r->executeWith(array());
    return \lang\XPClass::forName($r->type()->name());
  }
}
