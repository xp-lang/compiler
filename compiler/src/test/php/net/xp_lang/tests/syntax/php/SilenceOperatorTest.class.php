<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\SilenceOperatorNode;
use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\CastNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class SilenceOperatorTest extends ParserTestCase {

  /**
   * Test @$a[0]
   *
   */
  #[@test]
  public function arrayGet() {
    $this->assertEquals(
      array(new SilenceOperatorNode(new ArrayAccessNode(new VariableNode('a'), new IntegerNode('0')))),
      $this->parse('@$a[0];')
    );
  }

  /**
   * Test @(string)$a
   *
   */
  #[@test]
  public function stringCast() {
    $this->assertEquals(
      array(new SilenceOperatorNode(new CastNode(array(
        'type'        => new TypeName('string'),
        'expression'  => new VariableNode('a')
      )))),
      $this->parse('@(string)$a;')
    );
  }
}
