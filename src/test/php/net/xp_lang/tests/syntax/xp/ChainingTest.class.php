<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\InstanceCallNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\InvocationNode;
use xp\compiler\ast\CastNode;
use xp\compiler\types\TypeName;

/**
 * TestCase for chaining
 */
class ChainingTest extends ParserTestCase {

  #[@test]
  public function fieldAccess() {
    $this->assertEquals(
      [new MemberAccessNode(new VariableNode('m'), 'member')],
      $this->parse('$m.member;')
    );
  }

  #[@test]
  public function chainedFieldAccess() {
    $this->assertEquals(
      [new MemberAccessNode(new MemberAccessNode(new VariableNode('m'), 'member'), 'data')],
      $this->parse('$m.member.data;')
    );
  }

  #[@test]
  public function fieldNamedClassAccess() {
    $this->assertEquals(
      [new MemberAccessNode(new VariableNode('m'), 'class')],
      $this->parse('$m.class;')
    );
  }

  #[@test]
  public function method_call() {
    $this->assertEquals(
      [new MethodCallNode(new VariableNode('m'), 'func', [new VariableNode('args')])],
      $this->parse('$m.func($args);')
    );
  }

  #[@test]
  public function method_call_on_chained_fields() {
    $this->assertEquals(
      [new MethodCallNode(new MemberAccessNode(new MemberAccessNode(new VariableNode('m'), 'member'), 'data'), 'invoke', [new VariableNode('args')])],
      $this->parse('$m.member.data.invoke($args);')
    );
  }

  #[@test]
  public function method_call_on_chained_method_call() {
    $this->assertEquals(
      [new MethodCallNode(
        new MethodCallNode(new VariableNode('l'), 'withAppender'),
        'debug'
      )],
      $this->parse('$l.withAppender().debug();')
    );
  }

  #[@test]
  public function member_instance_call() {
    $this->assertEquals(
      [new InstanceCallNode(new BracedExpressionNode(new MemberAccessNode(new VariableNode('m'), 'func')), [new VariableNode('args')])],
      $this->parse('($m.func)($args);')
    );
  }

  #[@test]
  public function member_instance_call_chained_to_method_call() {
    $this->assertEquals(
      [new InstanceCallNode(new MethodCallNode(new VariableNode('m'), 'func', [new VariableNode('args')]), [new VariableNode('n')])],
      $this->parse('$m.func($args)($n);')
    );
  }

  #[@test]
  public function chainedAfterNew() {
    $this->assertEquals(
      [new MethodCallNode(
        new InstanceCreationNode([
          'type'           => new TypeName('Date'),
          'parameters'     => null,
        ]),
        'toString'
      )], 
      $this->parse('new Date().toString();')
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
      $this->parse('$l.elements()[0].name;')
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
      $this->parse('Logger::getInstance().configure("etc");')
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
      $this->parse('create($a).equals($b);')
    );
  }

  #[@test]
  public function chainedAfterBraced() {
    $this->assertEquals(
      [new MethodCallNode(
        new BracedExpressionNode(new CastNode([
          'type'       => new TypeName('Generic'),
          'expression' => new VariableNode('a')
        ])),
        'equals', 
        [new VariableNode('b')]
      )], 
      $this->parse('($a as Generic).equals($b);')
    );
  }
}
