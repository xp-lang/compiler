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
      array(new MemberAccessNode(new VariableNode('m'), 'member')),
      $this->parse('$m.member;')
    );
  }

  #[@test]
  public function chainedFieldAccess() {
    $this->assertEquals(
      array(new MemberAccessNode(new MemberAccessNode(new VariableNode('m'), 'member'), 'data')),
      $this->parse('$m.member.data;')
    );
  }

  #[@test]
  public function fieldNamedClassAccess() {
    $this->assertEquals(
      array(new MemberAccessNode(new VariableNode('m'), 'class')),
      $this->parse('$m.class;')
    );
  }

  #[@test]
  public function method_call() {
    $this->assertEquals(
      array(new MethodCallNode(new VariableNode('m'), 'func', array(new VariableNode('args')))),
      $this->parse('$m.func($args);')
    );
  }

  #[@test]
  public function method_call_on_chained_fields() {
    $this->assertEquals(
      array(new MethodCallNode(new MemberAccessNode(new MemberAccessNode(new VariableNode('m'), 'member'), 'data'), 'invoke', array(new VariableNode('args')))),
      $this->parse('$m.member.data.invoke($args);')
    );
  }

  #[@test]
  public function method_call_on_chained_method_call() {
    $this->assertEquals(
      array(new MethodCallNode(
        new MethodCallNode(new VariableNode('l'), 'withAppender'),
        'debug'
      )),
      $this->parse('$l.withAppender().debug();')
    );
  }

  #[@test]
  public function member_instance_call() {
    $this->assertEquals(
      array(new InstanceCallNode(new BracedExpressionNode(new MemberAccessNode(new VariableNode('m'), 'func')), array(new VariableNode('args')))),
      $this->parse('($m.func)($args);')
    );
  }

  #[@test]
  public function member_instance_call_chained_to_method_call() {
    $this->assertEquals(
      array(new InstanceCallNode(new MethodCallNode(new VariableNode('m'), 'func', array(new VariableNode('args'))), array(new VariableNode('n')))),
      $this->parse('$m.func($args)($n);')
    );
  }

  #[@test]
  public function chainedAfterNew() {
    $this->assertEquals(
      array(new MethodCallNode(
        new InstanceCreationNode(array(
          'type'           => new TypeName('Date'),
          'parameters'     => null,
        )),
        'toString'
      )), 
      $this->parse('new Date().toString();')
    );
  }

  #[@test]
  public function arrayOffsetOnMethod() {
    $this->assertEquals(
      array(new MemberAccessNode(
        new ArrayAccessNode(
          new MethodCallNode(new VariableNode('l'), 'elements', null),
          new IntegerNode('0')
        ),
        'name'
      )),
      $this->parse('$l.elements()[0].name;')
    );
  }

  #[@test]
  public function chainedAfterStaticMethod() {
    $this->assertEquals(
      array(new MethodCallNode(
        new StaticMethodCallNode(new TypeName('Logger'), 'getInstance', array()),
        'configure', 
        array(new StringNode('etc'))
      )), 
      $this->parse('Logger::getInstance().configure("etc");')
    );
  }

  #[@test]
  public function chainedAfterFunction() {
    $this->assertEquals(
      array(new MethodCallNode(
        new InvocationNode('create', array(new VariableNode('a'))),
        'equals', 
        array(new VariableNode('b'))
      )), 
      $this->parse('create($a).equals($b);')
    );
  }

  #[@test]
  public function chainedAfterBraced() {
    $this->assertEquals(
      array(new MethodCallNode(
        new BracedExpressionNode(new CastNode(array(
          'type'       => new TypeName('Generic'),
          'expression' => new VariableNode('a')
        ))),
        'equals', 
        array(new VariableNode('b'))
      )), 
      $this->parse('($a as Generic).equals($b);')
    );
  }
}
