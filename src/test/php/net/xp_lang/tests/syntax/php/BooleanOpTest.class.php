<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\BooleanOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\AssignmentNode;

class BooleanOpTest extends ParserTestCase {

  #[@test]
  public function booleanOr() {
    $this->assertEquals(array(new BooleanOpNode(array(
      'lhs'           => new VariableNode('a'),
      'rhs'           => new VariableNode('b'),
      'op'            => '||'
    ))), $this->parse('$a || $b;'));
  }

  #[@test]
  public function booleanAnd() {
    $this->assertEquals(array(new BooleanOpNode(array(
      'lhs'           => new VariableNode('a'),
      'rhs'           => new VariableNode('b'),
      'op'            => '&&'
    ))), $this->parse('$a && $b;'));
  }

  #[@test]
  public function conditionalAssignment() {
    $this->assertEquals(array(new BooleanOpNode(array(
      'lhs'           => new VariableNode('a'),
      'rhs'           => new AssignmentNode(array(
        'variable'      => new VariableNode('b'),
        'expression'    => new IntegerNode('1'),
        'op'            => '+='
      )),
      'op'            => '&&'
    ))), $this->parse('$a && $b+= 1;'));
  }
}