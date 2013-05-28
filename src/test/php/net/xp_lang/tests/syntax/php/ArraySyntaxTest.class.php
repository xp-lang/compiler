<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\VariableNode;

/**
 * TestCase
 *
 */
class ArraySyntaxTest extends ParserTestCase {

  /**
   * Test [1]
   *
   */
  #[@test]
  public function integerOffset() {
    $this->assertEquals(
      array(new ArrayAccessNode(new VariableNode('b'), new IntegerNode('1'))),
      $this->parse('$b[1];')
    );
  }

  /**
   * Test ["a"]
   *
   */
  #[@test]
  public function stringOffset() {
    $this->assertEquals(
      array(new ArrayAccessNode(new VariableNode('b'), new StringNode('a'))),
      $this->parse('$b["a"];')
    );
  }

  /**
   * Test []
   *
   */
  #[@test]
  public function noOffset() {
    $this->assertEquals(
      array(new ArrayAccessNode(new VariableNode('b'), null)),
      $this->parse('$b[];')
    );
  }

  /**
   * Test $str{$i}
   *
   */
  #[@test]
  public function curlyBraces() {
    $this->assertEquals(
      array(new ArrayAccessNode(new VariableNode('str'), new VariableNode('i'))),
      $this->parse('$str{$i};')
    );
  }
}
