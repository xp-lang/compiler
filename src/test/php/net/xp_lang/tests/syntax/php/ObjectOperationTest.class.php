<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\CloneNode;
use xp\compiler\ast\InstanceOfNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\types\TypeName;

class ObjectOperationTest extends ParserTestCase {

  #[@test]
  public function instanceCreation() {
    $this->assertEquals(
      array(new InstanceCreationNode(array(
        'type'       => new TypeName('XPClass'),
        'parameters' => null
      ))),
      $this->parse('new XPClass();')
    );
  }

  #[@test]
  public function cloningOperation() {
    $this->assertEquals(
      array(new CloneNode(new VariableNode('b'))),
      $this->parse('clone $b;')
    );
  }

  #[@test]
  public function instanceOfTest() {
    $this->assertEquals(
      array(new InstanceOfNode(array(
        'expression' => new VariableNode('b'), 
        'type'       => new TypeName('XPClass')
      ))),
      $this->parse('$b instanceof XPClass;')
    );
  }

  #[@test, @expect('lang.FormatException')]
  public function newWithoutBraces() {
    $this->parse('new Object;');
  }
}
