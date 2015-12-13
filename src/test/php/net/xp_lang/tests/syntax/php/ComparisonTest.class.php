<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\ComparisonNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\UnaryOpNode;

class ComparisonTest extends ParserTestCase {

  #[@test]
  public function equality() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '=='
    ])], $this->parse('
      $i == 10;
    '));
  }

  #[@test]
  public function identity() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '==='
    ])], $this->parse('
      $i === 10;
    '));
  }

  #[@test]
  public function notIdentity() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '!=='
    ])], $this->parse('
      $i !== 10;
    '));
  }

  #[@test]
  public function equalityToNegativeLhs() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new UnaryOpNode([
        'expression'    => new IntegerNode('10'),
        'op'            => '-'
      ]),
      'rhs'           => new VariableNode('i'),
      'op'            => '=='
    ])], $this->parse('
      -10 == $i;
    '));
  }

  #[@test]
  public function unEquality() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '!='
    ])], $this->parse('
      $i != 10;
    '));
  }

  #[@test]
  public function smallerThan() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '<'
    ])], $this->parse('
      $i < 10;
    '));
  }

  #[@test]
  public function smallerThanOrEqualTo() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '<='
    ])], $this->parse('
      $i <= 10;
    '));
  }

  #[@test]
  public function greaterThan() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '>'
    ])], $this->parse('
      $i > 10;
    '));
  }

  #[@test]
  public function greaterThanOrEqualTo() {
    $this->assertEquals([new ComparisonNode([
      'lhs'           => new VariableNode('i'),
      'rhs'           => new IntegerNode('10'),
      'op'            => '>='
    ])], $this->parse('
      $i >= 10;
    '));
  }
}
