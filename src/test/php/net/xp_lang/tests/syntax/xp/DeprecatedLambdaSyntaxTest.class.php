<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\LambdaNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\InstanceCallNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 * @deprecated See LambdaTest for new syntax
 */
class DeprecatedLambdaSyntaxTest extends ParserTestCase {

  #[@test]
  public function expression() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('#{ $a -> $a + 1 };')
    );
  }

  #[@test]
  public function statement() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('#{ $a -> { return $a + 1; } };')
    );
  }

  #[@test]
  public function multipleStatements() {
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
      $this->parse('#{ $a -> { $a+= 1; return $a; } };')
    );
  }

  #[@test]
  public function noStatements() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array()
      )), 
      $this->parse('#{ $a -> { } };')
    );
  }

  #[@test]
  public function typedParameterWithBrackets() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('#{ int $a -> { return $a + 1; } };')
    );
  }

  #[@test]
  public function parametersWithBrackets() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a'), array('name' => 'b')),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('#{ $a, $b -> { return $a + $b; } };')
    );
  }

  #[@test]
  public function typedParametersWithBrackets() {
    $this->assertEquals(
      array(new LambdaNode(
        array(array('name' => 'a', 'type' => new TypeName('int')), array('name' => 'b', 'type' => new TypeName('int'))),
        array(new ReturnNode(new BinaryOpNode(array(
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ))))
      )), 
      $this->parse('#{ int $a, int $b -> { return $a + $b; } };')
    );
  }

  #[@test]
  public function emptyParameters() {
    $this->assertEquals(
      array(new LambdaNode(
        array(),
        array(new ReturnNode(new StaticMethodCallNode(
          new TypeName('Console'),
          'write', 
          array(new StringNode('Hello'))
        )))
      )), 
      $this->parse('#{ -> Console::write("Hello") };')
    );
  }

  #[@test]
  public function invocation() {
    $this->assertEquals(
      array(new InstanceCallNode(new LambdaNode(
        array(),
        array(new ReturnNode(new StaticMethodCallNode(
          new TypeName('Console'),
          'write', 
          array(new StringNode('Hello'))
        )))
      ))), 
      $this->parse('#{ -> Console::write("Hello") }();')
    );
  }
}
