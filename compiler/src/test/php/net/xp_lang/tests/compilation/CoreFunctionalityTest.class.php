<?php namespace net\xp_lang\tests\compilation;

use xp\compiler\Syntax;
use xp\compiler\emit\source\Emitter;
use xp\compiler\types\TypeDeclarationScope;

/**
 * Tests core functionality
 */
class CoreFunctionalityTest extends \unittest\TestCase {
  const TEMPLATE = '<?php class Test { public function test() { %s(); }}';

  /**
   * Returns list of core functions
   *
   * @return var[]
   */
  protected function functions() {
    return array(
      'newinstance',
      'with',
      'create',
      'raise',
      'delete',
      'cast',
      'is',
      'this'
    );
  }

  /**
   * Parse source
   *
   * @param  string $source
   * @return xp.compiler.ast.ParseTree
   */
  protected function parse($source) {
    return Syntax::forName('php')->parse(new \io\streams\MemoryInputStream($source), $this->name);
  }

  #[@test, @values('functions')]
  public function parsed_as_invocation_node($func) {
    $this->assertEquals(
      new \xp\compiler\ast\InvocationNode($func),
      $this->parse(sprintf(self::TEMPLATE, $func))->declaration->body[0]->body[0]
    );
  }

  #[@test, @values('functions')]
  public function emitted_without_error($func) {
    $this->assertInstanceOf(
      'xp.compiler.emit.source.Result',
      create(new Emitter())->emit($this->parse(sprintf(self::TEMPLATE, $func)), new TypeDeclarationScope())
    );
  }
}
