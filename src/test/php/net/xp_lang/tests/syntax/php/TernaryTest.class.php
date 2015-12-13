<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\TernaryNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\BracedExpressionNode;

class TernaryTest extends ParserTestCase {

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
