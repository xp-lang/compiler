<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\IntegerNode;

class ArrayTest extends ParserTestCase {

  #[@test, @values([
  #  'array();',
  #  '[];'
  #])]
  public function emptyUntypedArray($syntax) {
    $this->assertEquals([new ArrayNode([
      'values'        => null,
      'type'          => null,
    ])], $this->parse($syntax));
  }

  #[@test, @values([
  #  'array(1, 2, 3);',
  #  '[1, 2, 3];'
  #])]
  public function untypedArray($syntax) {
    $this->assertEquals([new ArrayNode([
      'values'        => [
        new IntegerNode('1'),
        new IntegerNode('2'),
        new IntegerNode('3'),
      ],
      'type'          => null,
    ])], $this->parse($syntax));
  }

  #[@test, @values([
  #  'array(1, 2, 3, );',
  #  '[1, 2, 3, ];'
  #])]
  public function untypedArrayWithDanglingComma($syntax) {
    $this->assertEquals([new ArrayNode([
      'values'        => [
        new IntegerNode('1'),
        new IntegerNode('2'),
        new IntegerNode('3'),
      ],
      'type'          => null,
    ])], $this->parse($syntax));
  }
}
