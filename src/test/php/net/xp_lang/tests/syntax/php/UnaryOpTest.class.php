<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;

class UnaryOpTest extends ParserTestCase {

  /**
   * Assertion helper
   *
   * @param  xp.compiler.ast.Node $offset
   * @param  string $syntax
   * @throws unittest.AssertionFailedError
   */
  private function assertUnaryOp($operator, $syntax) {
    $this->assertEquals(
      [new UnaryOpNode([
        'expression'    => new VariableNode('i'),
        'op'            => $operator
      ])],
      $this->parse($syntax)
    );
  }

  #[@test]
  public function negation() {
    $this->assertUnaryOp('!', '!$i;');
  }

  #[@test]
  public function complement() {
    $this->assertUnaryOp('~', '~$i;');
  }

  #[@test]
  public function increment() {
    $this->assertUnaryOp('++', '++$i;');
  }

  #[@test]
  public function decrement() {
    $this->assertUnaryOp('--', '--$i;');
  }

  #[@test]
  public function bitwise_not() {
    $this->assertUnaryOp('~', '~$i;');
  }
}