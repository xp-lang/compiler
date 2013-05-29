<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\types\TypeName;

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
      'values'        => array(),
      'type'          => null,
    ))), $this->parse('
      [];
    '));
  }

  /**
   * Test an empty typed array
   *
   */
  #[@test]
  public function emptyTypedArray() {
    $this->assertEquals(array(new ArrayNode(array(
      'values'        => array(),
      'type'          => new TypeName('int[]'),
    ))), $this->parse('
      new int[] {};
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
      [1, 2, 3];
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
      [1, 2, 3, ];
    '));
  }

  /**
   * Test a non-empty typed array
   *
   */
  #[@test]
  public function typedArray() {
    $this->assertEquals(array(new ArrayNode(array(
      'values'        => array(
        new IntegerNode('1'),
        new IntegerNode('2'),
        new IntegerNode('3'),
      ),
      'type'          => new TypeName('int[]'),
    ))), $this->parse('
      new int[] {1, 2, 3};
    '));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function arrayTypeArray() {
    $this->assertEquals(
      array(new ArrayNode(array(
        'values'        => array(),
        'type'          => new TypeName('string[][]'),
      ))),
      $this->parse('new string[][] {};')
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function mapTypeArray() {
    $this->assertEquals(
      array(new ArrayNode(array(
        'values'        => array(),
        'type'          => new TypeName('[:var][]'),
      ))),
      $this->parse('new [:var][] {};')
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function genericTypeArray() {
    $this->assertEquals(
      array(new ArrayNode(array(
        'values'        => array(),
        'type'          => new TypeName('List<string>[]'),
      ))),
      $this->parse('new List<string>[] {};')
    );
  }
}
