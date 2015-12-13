<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\TernaryNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\ast\AssignmentNode;

/**
 * TestCase
 *
 */
class TernaryTest extends ParserTestCase {

  /**
   * Test ternary - expr ? expr : expr
   *
   */
  #[@test]
  public function ternary() {
    $this->assertEquals([new TernaryNode([
      'condition'     => new VariableNode('i'),
      'expression'    => new IntegerNode('1'),
      'conditional'   => new IntegerNode('2'),
    ])], $this->parse('
      $i ? 1 : 2;
    '));
  }

  /**
   * Test ternary - expr ?: expr
   *
   */
  #[@test]
  public function assignment() {
    $this->assertEquals([new AssignmentNode([
      'variable'      => new VariableNode('a'),
      'expression'    => new TernaryNode([
        'condition'     => new VariableNode('argc'),
        'expression'    => new VariableNode('args0'),
        'conditional'   => new IntegerNode('1')
      ]),
      'op'            => '='
    ])], $this->parse('
      $a= $argc ? $args0 : 1;
    '));
  }

  /**
   * Test ternary - expr ?: expr
   *
   */
  #[@test]
  public function withoutExpression() {
    $this->assertEquals([new TernaryNode([
      'condition'     => new VariableNode('i'),
      'expression'    => null,
      'conditional'   => new IntegerNode('2'),
    ])], $this->parse('
      $i ?: 2;
    '));
  }

  /**
   * Test ternary - expr ?: (expr ? expr : expr)
   *
   */
  #[@test]
  public function nested() {
    $this->assertEquals([new TernaryNode([
      'condition'     => new VariableNode('i'),
      'expression'    => null,
      'conditional'   => new BracedExpressionNode(new TernaryNode([
        'condition'     => new VariableNode('f'),
        'expression'    => new IntegerNode('1'),
        'conditional'   => new IntegerNode('2'),
      ]))
    ])], $this->parse('
      $i ?: ($f ? 1 : 2);
    '));
  }
}
