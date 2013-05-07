<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;

/**
 * TestCase
 *
 * @see   php://language.operators.bitwise
 */
class BitOperatorsTest extends ParserTestCase {

  /**
   * Test "|" operator
   *
   */
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

  /**
   * Test "&" operator
   *
   */
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

  /**
   * Test "^" operator
   *
   */
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

  /**
   * Test "~" prefix operator
   *
   */
  #[@test]
  public function bitwiseNot() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new IntegerNode('1'),
      'postfix'       => FALSE,
      'op'            => '~'
    ))), $this->parse('
      ~1;
    '));
  }

  /**
   * Test "<<" operator
   *
   */
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

  /**
   * Test ">>" operator
   *
   */
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
