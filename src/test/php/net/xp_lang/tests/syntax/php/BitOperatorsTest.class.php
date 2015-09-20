<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;

class BitOperatorsTest extends ParserTestCase {

  #[@test]
  public function bitwiseOr() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '|'
    ))), $this->parse('
      1 | 2;
    '));
  }

  #[@test]
  public function bitwiseAnd() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '&'
    ))), $this->parse('
      1 & 2;
    '));
  }

  #[@test]
  public function bitwiseXOr() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '^'
    ))), $this->parse('
      1 ^ 2;
    '));
  }

  #[@test]
  public function bitwiseNot() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new IntegerNode('1'),
      'postfix'       => false,
      'op'            => '~'
    ))), $this->parse('
      ~1;
    '));
  }

  #[@test]
  public function shiftLeft() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '<<'
    ))), $this->parse('
      1 << 2;
    '));
  }

  #[@test]
  public function shiftRight() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '>>'
    ))), $this->parse('
      1 >> 2;
    '));
  }
}
