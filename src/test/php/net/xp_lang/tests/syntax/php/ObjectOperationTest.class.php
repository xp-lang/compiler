<?php namespace net\xp_lang\tests\syntax\php;

use lang\FormatException;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\CloneNode;
use xp\compiler\ast\InstanceOfNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\types\TypeName;

class ObjectOperationTest extends ParserTestCase {

  #[@test]
  public function instanceCreation() {
    $this->assertEquals(
      [new InstanceCreationNode([
        'type'       => new TypeName('XPClass'),
        'parameters' => null
      ])],
      $this->parse('new XPClass();')
    );
  }

  #[@test]
  public function cloningOperation() {
    $this->assertEquals(
      [new CloneNode(new VariableNode('b'))],
      $this->parse('clone $b;')
    );
  }

  #[@test]
  public function instanceOfTest() {
    $this->assertEquals(
      [new InstanceOfNode([
        'expression' => new VariableNode('b'), 
        'type'       => new TypeName('XPClass')
      ])],
      $this->parse('$b instanceof XPClass;')
    );
  }

  #[@test, @expect(FormatException::class)]
  public function newWithoutBraces() {
    $this->parse('new Object;');
  }
}
