<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\BracedExpressionNode;

/**
 * TestCase
 *
 */
class BinaryOpTest extends ParserTestCase {

  /**
   * Test addition operator
   *
   */
  #[@test]
  public function addition() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '+'
    ))), $this->parse('$i + 10;'));
  }

  /**
   * Test subtraction operator
   *
   */
  #[@test]
  public function subtraction() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '-'
    ))), $this->parse('$i - 10;'));
  }

  /**
   * Test multiplication operator
   *
   */
  #[@test]
  public function multiplication() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '*'
    ))), $this->parse('$i * 10;'));
  }

  /**
   * Test exponentiation  operator
   *
   */
  #[@test]
  public function exponentiation() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '**'
    ))), $this->parse('$i ** 10;'));
  }

  /**
   * Test division operator
   *
   */
  #[@test]
  public function division() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '/'
    ))), $this->parse('$i / 10;'));
  }

  /**
   * Test modulo operator
   *
   */
  #[@test]
  public function modulo() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '%'
    ))), $this->parse('$i % 10;'));
  }

  /**
   * Test brackets used for precedence
   *
   */
  #[@test]
  public function bracketsUsedForPrecedence() {
    $this->assertEquals(
      array(new BinaryOpNode(array(
        'lhs' => new BracedExpressionNode(new BinaryOpNode(array(
          'lhs'    => new IntegerNode('5'),
          'rhs'    => new IntegerNode('6'),
          'op'     => '+'
        ))),
        'rhs' => new IntegerNode('3'),
        'op'  => '*'
      ))), 
      $this->parse('(5 + 6) * 3;')
    );
  }

  /**
   * Test brackets used for precedence
   *
   */
  #[@test]
  public function bracketsUsedForPrecedenceWithVariable() {
    $this->assertEquals(
      array(new BinaryOpNode(array(
        'lhs' => new BracedExpressionNode(new BinaryOpNode(array(
          'lhs'    => new VariableNode('i'),
          'rhs'    => new IntegerNode('6'),
          'op'     => '+'
        ))),
        'rhs' => new IntegerNode('3'),
        'op'  => '*'
      ))), 
      $this->parse('($i + 6) * 3;')
    );
  }

  /**
   * Test concatenation
   *
   */
  #[@test]
  public function concatenation() {
    $this->assertEquals(
      array(new BinaryOpNode(array(
        'lhs' => new StringNode('Hello'),
        'rhs' => new StringNode('World'),
        'op'  => '~'
      ))), 
      $this->parse('"Hello" ~ "World";')
    );
  }

  /**
   * Test concatenation
   *
   */
  #[@test]
  public function bracketsInConcatenation() {
    $this->assertEquals(
      array(new BinaryOpNode(array(
        'lhs' => new StringNode('Hello #'),
        'rhs' => new BracedExpressionNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('i'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))),
        'op'  => '~'
      ))), 
      $this->parse('"Hello #" ~ ($i + 1);')
    );
  }
}
