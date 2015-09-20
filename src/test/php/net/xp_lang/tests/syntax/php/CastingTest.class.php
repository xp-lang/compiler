<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\CastNode;
use xp\compiler\ast\InvocationNode;
use xp\compiler\types\TypeName;

class CastingTest extends ParserTestCase {

  #[@test]
  public function boolCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('bool'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (bool)1;'));
  }

  #[@test]
  public function stringCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('string'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (string)1;'));
  }

  #[@test]
  public function arrayCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('var[]'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (array)1;'));
  }

  #[@test]
  public function intCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('int'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (int)1;'));
  }

  #[@test]
  public function doubleCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('double'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (double)1;'));
  }

  #[@test]
  public function invocationWithConstantArg() {
    $this->assertEquals(
      array(new InvocationNode('init', array(new BooleanNode(true)))),
      $this->parse('init(true);')
    );
  }

  #[@test]
  public function castCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('bool'),
        'expression'    => new CastNode(array(
          'type'          => new TypeName('string'),
          'expression'    => new IntegerNode('1')
        )),
      )),
      'op'            => '=',
    ))), $this->parse('$a= (bool)(string)1;'));
  }
}
