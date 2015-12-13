<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\BooleanOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\AssignmentNode;

/**
 * Boolean operators (||, &&)
 */
class BooleanOpTest extends ParserTestCase {

  #[@test]
  public function boolean_or() {
    $this->assertEquals([new BooleanOpNode([
      'lhs'           => new VariableNode('a'),
      'rhs'           => new VariableNode('b'),
      'op'            => '||'
    ])], $this->parse('$a || $b;'));
  }

  #[@test]
  public function boolean_and() {
    $this->assertEquals([new BooleanOpNode([
      'lhs'           => new VariableNode('a'),
      'rhs'           => new VariableNode('b'),
      'op'            => '&&'
    ])], $this->parse('$a && $b;'));
  }

  /**
   * Test the following code:
   *
   * <code>
   *   $a && $b+= 1;
   * </code>
   */
  #[@test]
  public function conditional_assignment() {
    $this->assertEquals([new BooleanOpNode([
      'lhs'           => new VariableNode('a'),
      'rhs'           => new AssignmentNode([
        'variable'      => new VariableNode('b'),
        'expression'    => new IntegerNode('1'),
        'op'            => '+='
      ]),
      'op'            => '&&'
    ])], $this->parse('$a && $b+= 1;'));
  }
}
