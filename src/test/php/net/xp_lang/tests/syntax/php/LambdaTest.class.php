<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\LambdaNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\InstanceCallNode;
use xp\compiler\ast\BracedExpressionNode;

class LambdaTest extends ParserTestCase {

  #[@test]
  public function noParameters() {
    $this->assertEquals(
      [new LambdaNode(
        [],
        [new ReturnNode(new BooleanNode(true))],
        []
      )], 
      $this->parse('function() { return true; };')
    );
  }

  #[@test]
  public function oneParameter() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a']],
        [new ReturnNode(new BinaryOpNode([
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ]))],
        []
      )], 
      $this->parse('function($a) { return $a + 1; };')
    );
  }

  #[@test]
  public function twoParameters() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a'], ['name' => 'b']],
        [new ReturnNode(new BinaryOpNode([
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ]))],
        []
      )], 
      $this->parse('function($a, $b) { return $a + $b; };')
    );
  }

  #[@test]
  public function withUses() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a']],
        [/* TBI */],
        [['name' => 'mul'], ['name' => 'neg']]
      )], 
      $this->parse('function($a) use($mul, $neg) { /* TBI */ };')
    );
  }

  #[@test]
  public function invocation() {
    $this->assertEquals(
      [new InstanceCallNode(
        new BracedExpressionNode(new LambdaNode(
          [['name' => 'a'], ['name' => 'b']],
          [/* TBI */],
          []
        )),
        [new IntegerNode('1'), new IntegerNode('2')]
      )],
      $this->parse('(function($a, $b) { /* TBI */ })(1, 2);')
    );
  }
}