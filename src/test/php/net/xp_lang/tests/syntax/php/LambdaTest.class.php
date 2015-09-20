<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\LambdaNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BinaryOpNode;

class LambdaTest extends ParserTestCase {

  #[@test]
  public function noParameters() {
    $this->assertEquals(
      array(new LambdaNode(
        array(),
        array(new ReturnNode(new BooleanNode(true))),
        array()
      )), 
      $this->parse('function() { return true; };')
    );
  }

  #[@test]
  public function oneParameter() {
    $this->assertEquals(
      array(new LambdaNode(
        array(new VariableNode('a')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        )))),
        array()
      )), 
      $this->parse('function($a) { return $a + 1; };')
    );
  }

  #[@test]
  public function twoParameters() {
    $this->assertEquals(
      array(new LambdaNode(
        array(new VariableNode('a'), new VariableNode('b')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        )))),
        array()
      )), 
      $this->parse('function($a, $b) { return $a + $b; };')
    );
  }

  #[@test]
  public function withUses() {
    $this->assertEquals(
      array(new LambdaNode(
        array(new VariableNode('a')),
        array(/* TBI */),
        array(new VariableNode('mul'), new VariableNode('neg'))
      )), 
      $this->parse('function($a) use($mul, $neg) { /* TBI */ };')
    );
  }
}