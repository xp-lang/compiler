<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\LambdaNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\InstanceCallNode;
use xp\compiler\ast\BracedExpressionNode;

/**
 * TestCase
 *
 */
class LambdaTest extends ParserTestCase {

  /**
   * Test simple lambda
   *
   */
  #[@test]
  public function noParameters() {
    $this->assertEquals(
      array(new LambdaNode(
        array(),
        array(new ReturnNode(new BooleanNode(true)))
      )), 
      $this->parse('function() { return true; };')
    );
  }

  /**
   * Test simple lambda
   *
   */
  #[@test]
  public function oneParameter() {
    $this->assertEquals(
      array(new LambdaNode(
        array(new VariableNode('a')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('function($a) { return $a + 1; };')
    );
  }

  /**
   * Test simple lambda
   *
   */
  #[@test]
  public function twoParameters() {
    $this->assertEquals(
      array(new LambdaNode(
        array(new VariableNode('a'), new VariableNode('b')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('function($a, $b) { return $a + $b; };')
    );
  }

  #[@test]
  public function invocation() {
    $this->assertEquals(
      array(new InstanceCallNode(
        new BracedExpressionNode(new LambdaNode(
          array(new VariableNode('a'), new VariableNode('b')),
          array(/* TBI */)
        )),
        array(new IntegerNode('1'), new IntegerNode('2'))
      )),
      $this->parse('(function($a, $b) { /* TBI */ })(1, 2);')
    );
  }
}