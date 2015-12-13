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
    $this->assertEquals([new AssignmentNode([
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode([
        'type'          => new TypeName('bool'),
        'expression'    => new IntegerNode('1')
      ]),
      'op'            => '=',
    ])], $this->parse('$a= (bool)1;'));
  }

  #[@test]
  public function stringCast() {
    $this->assertEquals([new AssignmentNode([
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode([
        'type'          => new TypeName('string'),
        'expression'    => new IntegerNode('1')
      ]),
      'op'            => '=',
    ])], $this->parse('$a= (string)1;'));
  }

  #[@test]
  public function arrayCast() {
    $this->assertEquals([new AssignmentNode([
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode([
        'type'          => new TypeName('var[]'),
        'expression'    => new IntegerNode('1')
      ]),
      'op'            => '=',
    ])], $this->parse('$a= (array)1;'));
  }

  #[@test]
  public function intCast() {
    $this->assertEquals([new AssignmentNode([
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode([
        'type'          => new TypeName('int'),
        'expression'    => new IntegerNode('1')
      ]),
      'op'            => '=',
    ])], $this->parse('$a= (int)1;'));
  }

  #[@test]
  public function doubleCast() {
    $this->assertEquals([new AssignmentNode([
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode([
        'type'          => new TypeName('double'),
        'expression'    => new IntegerNode('1')
      ]),
      'op'            => '=',
    ])], $this->parse('$a= (double)1;'));
  }

  #[@test]
  public function invocationWithConstantArg() {
    $this->assertEquals(
      [new InvocationNode('init', [new BooleanNode(true)])],
      $this->parse('init(true);')
    );
  }

  #[@test]
  public function castCast() {
    $this->assertEquals([new AssignmentNode([
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode([
        'type'          => new TypeName('bool'),
        'expression'    => new CastNode([
          'type'          => new TypeName('string'),
          'expression'    => new IntegerNode('1')
        ]),
      ]),
      'op'            => '=',
    ])], $this->parse('$a= (bool)(string)1;'));
  }
}
