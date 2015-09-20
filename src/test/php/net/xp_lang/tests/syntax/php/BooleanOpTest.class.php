<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\BooleanOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\AssignmentNode;

class BooleanOpTest extends ParserTestCase {

  /**
   * Assertion helper
   *
   * @param  xp.compiler.ast.Node $offset
   * @param  string $syntax
   * @throws unittest.AssertionFailedError
   */
  private function assertBooleanOp($operator, $syntax) {
    $this->assertEquals(
      [new BooleanOpNode([
        'lhs'           => new VariableNode('a'),
        'rhs'           => new VariableNode('b'),
        'op'            => $operator
      ])],
      $this->parse($syntax)
    );
  }

  #[@test]
  public function booleanOr() {
    $this->assertBooleanOp('||', '$a || $b;');
  }

  #[@test]
  public function booleanAnd() {
    $this->assertBooleanOp('&&', '$a && $b;');
  }

  #[@test]
  public function conditionalAssignment() {
    $this->assertEquals(
      [new BooleanOpNode([
        'lhs'           => new VariableNode('a'),
        'rhs'           => new AssignmentNode([
          'variable'      => new VariableNode('b'),
          'expression'    => new IntegerNode('1'),
          'op'            => '+='
        ]),
        'op'            => '&&'
      ])],
      $this->parse('$a && $b+= 1;')
    );
  }
}