<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\ast\StringNode;

class BinaryOpTest extends ParserTestCase {

  /**
   * Assertion helper
   *
   * @param  xp.compiler.ast.Node $offset
   * @param  string $syntax
   * @throws unittest.AssertionFailedError
   */
  private function assertBinaryOp($operator, $syntax) {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('2'),
        'op'            => $operator
      ])],
      $this->parse($syntax)
    );
  }

  #[@test]
  public function addition() {
    $this->assertBinaryOp('+', '$i + 2;');
  }

  #[@test]
  public function subtraction() {
    $this->assertBinaryOp('-', '$i - 2;');
  }

  #[@test]
  public function multiplication() {
    $this->assertBinaryOp('*', '$i * 2;');
  }

  #[@test]
  public function division() {
    $this->assertBinaryOp('/', '$i / 2;');
  }

  #[@test]
  public function modulo() {
    $this->assertBinaryOp('%', '$i % 2;');
  }

  #[@test]
  public function bitwiseOr() {
    $this->assertBinaryOp('|', '$i | 2;');
  }

  #[@test]
  public function bitwiseAnd() {
    $this->assertBinaryOp('&', '$i & 2;');
  }

  #[@test]
  public function bitwiseXOr() {
    $this->assertBinaryOp('^', '$i ^ 2;');
  }

  #[@test]
  public function shiftLeft() {
    $this->assertBinaryOp('<<', '$i << 2;');
  }

  #[@test]
  public function shiftRight() {
    $this->assertBinaryOp('>>', '$i >> 2;');
  }

  #[@test]
  public function exponent() {
    $this->assertBinaryOp('**', '$i ** 2;');
  }

  #[@test]
  public function bracketsUsedForPrecedence() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new BracedExpressionNode(new BinaryOpNode([
          'lhs'    => new IntegerNode('5'),
          'rhs'    => new IntegerNode('6'),
          'op'     => '+'
        ])),
        'rhs' => new IntegerNode('3'),
        'op'  => '*'
      ])], 
      $this->parse('(5 + 6) * 3;')
    );
  }

  #[@test]
  public function bracketsUsedForPrecedenceWithVariable() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new BracedExpressionNode(new BinaryOpNode([
          'lhs'    => new VariableNode('i'),
          'rhs'    => new IntegerNode('6'),
          'op'     => '+'
        ])),
        'rhs' => new IntegerNode('3'),
        'op'  => '*'
      ])], 
      $this->parse('($i + 6) * 3;')
    );
  }

  #[@test]
  public function concatenation() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new StringNode('Hello'),
        'rhs' => new StringNode('World'),
        'op'  => '~'
      ])], 
      $this->parse('"Hello"."World";')
    );
  }

  #[@test]
  public function bracketsInConcatenation() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new StringNode('Hello #'),
        'rhs' => new BracedExpressionNode(new BinaryOpNode([
          'lhs' => new VariableNode('i'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ])),
        'op'  => '~'
      ])], 
      $this->parse('"Hello #".($i + 1);')
    );
  }

  #[@test]
  public function concatenation_string_variable_and_string() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new StringNode('/^'),
        'rhs' => new BinaryOpNode([
          'lhs' => new VariableNode('module'),
          'rhs' => new StringNode('@.+/'),
          'op'  => '~'
        ]),
        'op'  => '~'
      ])],
      $this->parse("'/^'.\$module.'@.+/';")
    );
  }
}
