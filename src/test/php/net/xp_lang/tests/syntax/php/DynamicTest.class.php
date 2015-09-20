<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\DynamicInstanceOfNode;
use xp\compiler\ast\DynamicInstanceCreationNode;
use xp\compiler\ast\DynamicVariableReferenceNode;
use xp\compiler\ast\VariableNode;

class DynamicTest extends ParserTestCase {

  #[@test]
  public function instanceOfVariable() {
    $this->assertEquals(array(new DynamicInstanceOfNode(array(
      'expression'    => new VariableNode('a'),
      'variable'      => 'type'
    ))), $this->parse('$a instanceof $type;'));
  }

  #[@test]
  public function instanceCreation() {
    $this->assertEquals(
      array(new DynamicInstanceCreationNode(array(
        'variable'    => 'type',
        'parameters'  => array()
      ))),
      $this->parse('new $type();')
    );
  }

  #[@test]
  public function variableMemberAccess() {
    $this->assertEquals(
      array(new DynamicVariableReferenceNode(new VariableNode('this'), new VariableNode('name'))),
      $this->parse('$this->$name;')
    );
  }

  #[@test]
  public function expressionMemberAccess() {
    $this->assertEquals(
      array(new DynamicVariableReferenceNode(new VariableNode('this'), new VariableNode('name'))),
      $this->parse('$this->{$name};')
    );
  }
}
