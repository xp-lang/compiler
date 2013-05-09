<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\InstanceCreationNode;
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

  /**
   * Test field access
   *
   */
  #[@test]
  public function fieldAccess() {
    $this->assertEquals(
      array(new MemberAccessNode(new VariableNode('m'), 'member')),
      $this->parse('$m.member;')
    );
  }

  /**
   * Test field access
   *
   */
  #[@test]
  public function chainedFieldAccess() {
    $this->assertEquals(
      array(new MemberAccessNode(new MemberAccessNode(new VariableNode('m'), 'member'), 'data')),
      $this->parse('$m.member.data;')
    );
  }

  /**
   * Test field access
   *
   */
  #[@test]
  public function fieldNamedClassAccess() {
    $this->assertEquals(
      array(new MemberAccessNode(new VariableNode('m'), 'class')),
      $this->parse('$m.class;')
    );
  }

  /**
   * Test simple method call on an object
   *
   */
  #[@test]
  public function methodCall() {
    $this->assertEquals(
      array(new MethodCallNode(new VariableNode('m'), 'invoke', array(new VariableNode('args')))),
      $this->parse('$m.invoke($args);')
    );
  }

  /**
   * Test simple method call on chained fields
   *
   */
  #[@test]
  public function methodCallOnChainedFields() {
    $this->assertEquals(
      array(new MethodCallNode(new MemberAccessNode(new MemberAccessNode(new VariableNode('m'), 'member'), 'data'), 'invoke', array(new VariableNode('args')))),
      $this->parse('$m.member.data.invoke($args);')
    );
  }

  /**
   * Test chained method calls
   *
   */
  #[@test]
  public function chainedMethodCalls() {
    $this->assertEquals(
      array(new MethodCallNode(
        new MethodCallNode(new VariableNode('l'), 'withAppender'),
        'debug'
      )),
      $this->parse('$l.withAppender().debug();')
    );
  }

  /**
   * Test chained method calls
   *
   */
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

  /**
   * Test chained method calls
   *
   */
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

  /**
   * Test chained method calls
   *
   */
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

  /**
   * Test chaining after function calls
   *
   */
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

  /**
   * Test chained after bracing
   *
   */
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
