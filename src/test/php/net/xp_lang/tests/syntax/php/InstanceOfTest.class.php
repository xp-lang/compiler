<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\InstanceOfNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class InstanceOfTest extends ParserTestCase {

  /**
   * Test instanceof a type
   *
   */
  #[@test]
  public function instanceOfObject() {
    $this->assertEquals(array(new InstanceOfNode(array(
      'expression'    => new VariableNode('a'),
      'type'          => new TypeName('Object'),
    ))), $this->parse('$a instanceof Object;'));
  }

  /**
   * Test instanceof a type
   *
   */
  #[@test]
  public function memberInstanceOfObject() {
    $this->assertEquals(array(new InstanceOfNode(array(
      'expression'    => new MemberAccessNode(new VariableNode('this'), 'a'),
      'type'          => new TypeName('Object'),
    ))), $this->parse('$this->a instanceof Object;'));
  }
}
