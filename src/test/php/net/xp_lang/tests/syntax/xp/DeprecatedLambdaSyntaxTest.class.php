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
      [new LambdaNode(
        [['name' => 'a']],
        [new ReturnNode(new BinaryOpNode([
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ]))]
      )], 
      $this->parse('#{ $a -> $a + 1 };')
    );
  }

  #[@test]
  public function statement() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a']],
        [new ReturnNode(new BinaryOpNode([
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ]))]
      )], 
      $this->parse('#{ $a -> { return $a + 1; } };')
    );
  }

  #[@test]
  public function multipleStatements() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a']],
        [
          new AssignmentNode([
            'variable'    => new VariableNode('a'),
            'expression'  => new IntegerNode('1'),
            'op'          => '+='
          ]),
          new ReturnNode(new VariableNode('a'))
        ]
      )], 
      $this->parse('#{ $a -> { $a+= 1; return $a; } };')
    );
  }

  #[@test]
  public function noStatements() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a']],
        []
      )], 
      $this->parse('#{ $a -> { } };')
    );
  }

  #[@test]
  public function typedParameterWithBrackets() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a']],
        [new ReturnNode(new BinaryOpNode([
          'lhs' => new VariableNode('a'),
          'rhs' => new IntegerNode('1'),
          'op'  => '+'
        ]))]
      )], 
      $this->parse('#{ int $a -> { return $a + 1; } };')
    );
  }

  #[@test]
  public function parametersWithBrackets() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a'], ['name' => 'b']],
        [new ReturnNode(new BinaryOpNode([
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ]))]
      )], 
      $this->parse('#{ $a, $b -> { return $a + $b; } };')
    );
  }

  #[@test]
  public function typedParametersWithBrackets() {
    $this->assertEquals(
      [new LambdaNode(
        [['name' => 'a', 'type' => new TypeName('int')], ['name' => 'b', 'type' => new TypeName('int')]],
        [new ReturnNode(new BinaryOpNode([
          'lhs' => new VariableNode('a'),
          'rhs' => new VariableNode('b'),
          'op'  => '+'
        ]))]
      )], 
      $this->parse('#{ int $a, int $b -> { return $a + $b; } };')
    );
  }

  #[@test]
  public function emptyParameters() {
    $this->assertEquals(
      [new LambdaNode(
        [],
        [new ReturnNode(new StaticMethodCallNode(
          new TypeName('Console'),
          'write', 
          [new StringNode('Hello')]
        ))]
      )], 
      $this->parse('#{ -> Console::write("Hello") };')
    );
  }

  #[@test]
  public function invocation() {
    $this->assertEquals(
      [new InstanceCallNode(new LambdaNode(
        [],
        [new ReturnNode(new StaticMethodCallNode(
          new TypeName('Console'),
          'write', 
          [new StringNode('Hello')]
        ))]
      ))], 
      $this->parse('#{ -> Console::write("Hello") }();')
    );
  }
}
