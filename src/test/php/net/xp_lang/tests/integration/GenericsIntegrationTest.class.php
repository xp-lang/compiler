<?php namespace net\xp_lang\tests\integration;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\StringSource;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\task\CompilationTask;
use xp\compiler\Syntax;

/**
 * TestCase
 */
class GenericsIntegrationTest extends \unittest\TestCase {
  protected static $syntax;
  protected $counter= 0;

  /**
   * Sets up compiler API
   */
  #[@beforeClass]
  public static function useXpSyntax() {
    self::$syntax= Syntax::forName('xp');
  }

  /**
   * Compile and then run sourcecode
   *
   * @param   string source
   * @return  xp.compiler.types.Types
   */
  protected function compile($source) {
    $decl= 'class FixtureGenericsIntegrationTest·%d { public void fixture() { %s }}';
    $emitter= new V53Emitter();
    $task= new CompilationTask(
      new StringSource(sprintf($decl, $this->counter++, $source), self::$syntax, $this->name),
      new NullDiagnosticListener(),
      new FileManager(),
      $emitter
    );
    return $task->run();
  }

  #[@test, @ignore('')]
  public function static_method_returning_generic() {
    $this->compile('net.xp_lang.tests.integration.Sequence::of([1, 2, 3]).toArray();');
  }
}
