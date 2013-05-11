<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\IntegerNode;

/**
 * TestCase
 *
 */
class ArrayTest extends ParserTestCase {

  /**
   * Test an empty untyped array
   *
   */
  #[@test]
  public function emptyUntypedArray() {
    $this->assertEquals(array(new ArrayNode(array(
      'values'        => null,
      'type'          => null,
    ))), $this->parse('
      array();
    '));
  }

  /**
   * Test a non-empty untyped array
   *
   */
  #[@test]
  public function untypedArray() {
    $this->assertEquals(array(new ArrayNode(array(
      'values'        => array(
        new IntegerNode('1'),
        new IntegerNode('2'),
        new IntegerNode('3'),
      ),
      'type'          => null,
    ))), $this->parse('
      array(1, 2, 3);
    '));
  }

  /**
   * Test a non-empty untyped array
   *
   */
  #[@test]
  public function untypedArrayWithDanglingComma() {
    $this->assertEquals(array(new ArrayNode(array(
      'values'        => array(
        new IntegerNode('1'),
        new IntegerNode('2'),
        new IntegerNode('3'),
      ),
      'type'          => null,
    ))), $this->parse('
      array(1, 2, 3, );
    '));
  }
}
