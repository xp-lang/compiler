<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\UnaryOpNode;

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
    $this->assertEquals([new BinaryOpNode([
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '|'
    ])], $this->parse('
      1 | 2;
    '));
  }

  /**
   * Test "&" operator
   *
   */
  #[@test]
  public function bitwiseAnd() {
    $this->assertEquals([new BinaryOpNode([
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '&'
    ])], $this->parse('
      1 & 2;
    '));
  }

  /**
   * Test "^" operator
   *
   */
  #[@test]
  public function bitwiseXOr() {
    $this->assertEquals([new BinaryOpNode([
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '^'
    ])], $this->parse('
      1 ^ 2;
    '));
  }

  /**
   * Test "~" prefix operator
   *
   */
  #[@test]
  public function bitwiseNot() {
    $this->assertEquals([new UnaryOpNode([
      'expression'    => new IntegerNode('1'),
      'postfix'       => false,
      'op'            => '~'
    ])], $this->parse('
      ~1;
    '));
  }

  /**
   * Test "<<" operator
   *
   */
  #[@test]
  public function shiftLeft() {
    $this->assertEquals([new BinaryOpNode([
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '<<'
    ])], $this->parse('
      1 << 2;
    '));
  }

  /**
   * Test ">>" operator
   *
   */
  #[@test]
  public function shiftRight() {
    $this->assertEquals([new BinaryOpNode([
      'lhs'           => new IntegerNode('1'),
      'rhs'           => new IntegerNode('2'),
      'op'            => '>>'
    ])], $this->parse('
      1 >> 2;
    '));
  }
}
