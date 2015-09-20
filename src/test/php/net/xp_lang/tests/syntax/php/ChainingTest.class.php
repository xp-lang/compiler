<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\InvocationNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\types\TypeName;

class ChainingTest extends ParserTestCase {

  #[@test]
  public function methodCall() {
    $this->assertEquals(
      [new MethodCallNode(new VariableNode('m'), 'invoke', [new VariableNode('args')])],
      $this->parse('$m->invoke($args);')
    );
  }

  #[@test]
  public function chainedMethodCalls() {
    $this->assertEquals(
      [new MethodCallNode(
        new MethodCallNode(new VariableNode('l'), 'withAppender', null),
        'debug',
        null
      )],
      $this->parse('$l->withAppender()->debug();')
    );
  }

  #[@test]
  public function chainedAfterNew() {
    $this->assertEquals(
      [new MethodCallNode(
        new BracedExpressionNode(new InstanceCreationNode(array(
          'type'           => new TypeName('Date'),
          'parameters'     => null,
        ))),
        'toString',
        null
      )], 
      $this->parse('(new Date())->toString();')
    );
  }

  #[@test]
  public function arrayOffsetOnMethod() {
    $this->assertEquals(
      [new MemberAccessNode(
        new ArrayAccessNode(
          new MethodCallNode(new VariableNode('l'), 'elements', null),
          new IntegerNode('0')
        ),
        'name'
      )],
      $this->parse('$l->elements()[0]->name;')
    );
  }

  #[@test]
  public function chainedAfterStaticMethod() {
    $this->assertEquals(
      [new MethodCallNode(
        new StaticMethodCallNode(new TypeName('Logger'), 'getInstance', []),
        'configure', 
        [new StringNode('etc')]
      )], 
      $this->parse('Logger::getInstance()->configure("etc");')
    );
  }

  #[@test]
  public function chainedAfterFunction() {
    $this->assertEquals(
      [new MethodCallNode(
        new InvocationNode('create', [new VariableNode('a')]),
        'equals',
        [new VariableNode('b')]
      )], 
      $this->parse('create($a)->equals($b);')
    );
  }

  #[@test]
  public function chainedAfterBraced() {
    $this->assertEquals(
      [new MethodCallNode(
        new BracedExpressionNode(new VariableNode('a')),
        'equals', 
        [new VariableNode('b')]
      )], 
      $this->parse('($a)->equals($b);')
    );
  }

  #[@test]
  public function arrayDereferencing() {
    $this->assertEquals(
      [new ArrayAccessNode(
        new ArrayNode(['values' => [new IntegerNode('1'), new IntegerNode('2'), new IntegerNode('3')]]),
        new IntegerNode('0')
      )],
      $this->parse('array(1, 2, 3)[0];')
    );
  }

  #[@test]
  public function stringDereferencing() {
    $this->assertEquals(
      [new ArrayAccessNode(
        new StringNode('Hello'),
        new IntegerNode('0')
      )],
      $this->parse('"Hello"[0];')
    );
  }
}
