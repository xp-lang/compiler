<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\InvocationNode;
use xp\compiler\ast\VariableNode;

/**
 * TestCase
 *
 */
class InvocationTest extends ParserTestCase {

  /**
   * Test writeLine()
   *
   */
  #[@test]
  public function writeLine() {
    $this->assertEquals(
      array(new InvocationNode('writeLine', array(new VariableNode('m')))),
      $this->parse('writeLine($m);')
    );
  }
}
