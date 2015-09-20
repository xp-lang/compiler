<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\YieldNode;
use xp\compiler\ast\YieldFromNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\types\TypeName;

class GeneratorTest extends ParserTestCase {

  #[@test]
  public function yield_without_value() {
    $this->assertEquals(
      [new YieldNode()],
      $this->parse('yield;')
    );
  }

  #[@test]
  public function yield_with_value() {
    $this->assertEquals(
      [new YieldNode(new IntegerNode('1'))],
      $this->parse('yield 1;')
    );
  }

  #[@test]
  public function yield_with_key_and_value() {
    $this->assertEquals(
      [new YieldNode(new IntegerNode('1'), new StringNode('number'))],
      $this->parse('yield "number" : 1;')
    );
  }

  #[@test]
  public function yield_from() {
    $this->assertEquals(
      [new YieldFromNode(new StaticMethodCallNode(new TypeName('self'), 'values', []))],
      $this->parse('yield from self::values();')
    );
  }
}