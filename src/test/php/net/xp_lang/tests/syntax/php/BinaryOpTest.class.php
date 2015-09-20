<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\ast\StringNode;

class BinaryOpTest extends ParserTestCase {

  #[@test]
  public function addition() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '+'
    ))), $this->parse('$i + 10;'));
  }

  #[@test]
  public function subtraction() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '-'
    ))), $this->parse('$i - 10;'));
  }

  #[@test]
  public function multiplication() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '*'
    ))), $this->parse('$i * 10;'));
  }

  #[@test]
  public function division() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '/'
    ))), $this->parse('$i / 10;'));
  }

  #[@test]
  public function modulo() {
    $this->assertEquals(array(new BinaryOpNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '%'
    ))), $this->parse('$i % 10;'));
  }

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

  #[@test]
  public function concatenation() {
    $this->assertEquals(
      array(new BinaryOpNode(array(
        'lhs' => new StringNode('Hello'),
        'rhs' => new StringNode('World'),
        'op'  => '~'
      ))), 
      $this->parse('"Hello"."World";')
    );
  }

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
      $this->parse('"Hello #".($i + 1);')
    );
  }

  #[@test]
  public function concatenation_string_variable_and_string() {
    $this->assertEquals(
      array(new BinaryOpNode(array(
        'lhs' => new StringNode('/^'),
        'rhs' => new BinaryOpNode(array(
          'lhs' => new VariableNode('module'),
          'rhs' => new StringNode('@.+/'),
          'op'  => '~'
        )),
        'op'  => '~'
      ))),
      $this->parse("'/^'.\$module.'@.+/';")
    );
  }
}
