<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\ComparisonNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\UnaryOpNode;

class ComparisonTest extends ParserTestCase {

  #[@test]
  public function equality() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '=='
    ))), $this->parse('
      $i == 10;
    '));
  }

  #[@test]
  public function identity() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '==='
    ))), $this->parse('
      $i === 10;
    '));
  }

  #[@test]
  public function notIdentity() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '!=='
    ))), $this->parse('
      $i !== 10;
    '));
  }

  #[@test]
  public function equalityToNegativeLhs() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new UnaryOpNode(array(
        'expression'    => new IntegerNode('10'),
        'op'            => '-'
      )),
      'rhs'           => new VariableNode('i'),
      'op'            => '=='
    ))), $this->parse('
      -10 == $i;
    '));
  }

  #[@test]
  public function unEquality() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '!='
    ))), $this->parse('
      $i != 10;
    '));
  }

  #[@test]
  public function smallerThan() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '<'
    ))), $this->parse('
      $i < 10;
    '));
  }

  #[@test]
  public function smallerThanOrEqualTo() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '<='
    ))), $this->parse('
      $i <= 10;
    '));
  }

  #[@test]
  public function greaterThan() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '>'
    ))), $this->parse('
      $i > 10;
    '));
  }

  #[@test]
  public function greaterThanOrEqualTo() {
    $this->assertEquals(array(new ComparisonNode(array(
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '>='
    ))), $this->parse('
      $i >= 10;
    '));
  }
}
