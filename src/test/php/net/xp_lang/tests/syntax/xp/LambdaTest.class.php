<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\LambdaNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\ComparisonNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\InstanceCallNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 */
class LambdaTest extends ParserTestCase {

  #[@test]
  public function binary_expression() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('$a -> $a + 1;')
    );
  }

  #[@test]
  public function compare_expression() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new ComparisonNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '>'
        ))))
      )), 
      $this->parse('$a -> $a > 1;')
    );
  }

  #[@test]
  public function statement_inside_block() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('$a -> { return $a + 1; };')
    );
  }

  #[@test]
  public function multiple_statements_inside_block() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(
          new AssignmentNode(array(
            'variable'    => new VariableNode('a'),
            'expression'  => new IntegerNode('1'),
            'op'          => '+='
          )),
          new ReturnNode(new VariableNode('a'))
        )
      )), 
      $this->parse('$a -> { $a+= 1; return $a; };')
    );
  }

  #[@test]
  public function no_statements_inside_block() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array()
      )), 
      $this->parse('$a -> { };')
    );
  }

  #[@test]
  public function typed_parameter_with_brackets() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a', 'type' => new TypeName('int'))),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('(int $a) -> { return $a + 1; };')
    );
  }

  #[@test]
  public function untyped_parameters_with_brackets() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a'), array('name' => 'b')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('($a, $b) -> { return $a + $b; };')
    );
  }

  #[@test]
  public function typed_parameters_with_brackets() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a', 'type' => new TypeName('int')), array('name' => 'b', 'type' => new TypeName('int'))),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('(int $a, int $b) -> { return $a + $b; };')
    );
  }

  #[@test]
  public function empty_parameters() {
    $this->assertEquals(
      array(new LambdaNode(
        array(),
        array(new ReturnNode(new StaticMethodCallNode(
          new TypeName('Console'),
          'write', 
          array(new StringNode('Hello'))
        )))
      )), 
      $this->parse('() -> Console::write("Hello");')
    );
  }

  #[@test]
  public function invocation() {
    $this->assertEquals(
      array(new InstanceCallNode(new BracedExpressionNode(new LambdaNode(
        array(),
        array(new ReturnNode(new StaticMethodCallNode(
          new TypeName('Console'),
          'write', 
          array(new StringNode('Hello'))
        )))
      )))), 
      $this->parse('(() -> Console::write("Hello"))();')
    );
  }

  #[@test]
  public function expression_returning_expression_with_braces() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new BracedExpressionNode(new LambdaNode(
          array(array('name' => 'a')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode('1'),
            'op'  => '+'
          ))))
        ))))
      )), 
      $this->parse('$a -> ($a -> $a + 1);')
    );
  }

  #[@test]
  public function expression_returning_expression() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new LambdaNode(
          array(array('name' => 'a')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode('1'),
            'op'  => '+'
          ))))
        )))
      )), 
      $this->parse('$a -> $a -> $a + 1;')
    );
  }

  #[@test]
  public function with_default_value() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a', 'type' => new TypeName('string'), 'default' => new NullNode())),
        array()
      )), 
      $this->parse('(string $a= null) -> { };')
    );
  }
}
