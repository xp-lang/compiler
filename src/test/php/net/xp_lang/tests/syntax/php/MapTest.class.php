<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\MapNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\StringNode;

class MapTest extends ParserTestCase {

  #[@test, @values([
  #  'array(:);',
  #  '[:];'
  #])]
  public function emptyUntypedArray($syntax) {
    $this->assertEquals(array(new MapNode(array(
      'elements'      => null,
      'type'          => null,
    ))), $this->parse($syntax));
  }

  #[@test, @values([
  #  'array(1 => "one", 2 => "two", 3 => "three");',
  #  '[1 => "one", 2 => "two", 3 => "three"];'
  #])]
  public function untypedMap($syntax) {
    $this->assertEquals(
      array(new MapNode(array(
        'elements'      => array(
          array(
            new IntegerNode('1'),
            new StringNode('one'),
          ),
          array(
            new IntegerNode('2'),
            new StringNode('two'),
          ),
          array(
            new IntegerNode('3'),
            new StringNode('three'),
          ),
        ),
        'type'          => null,
      ))), 
      $this->parse($syntax)
    );
  }

  #[@test, @values([
  #  'array(1 => "one", 2 => "two", 3 => "three", );',
  #  '[1 => "one", 2 => "two", 3 => "three", ];'
  #])]
  public function untypedMapWithDanglingComma($syntax) {
    $this->assertEquals(
      array(new MapNode(array(
        'elements'      => array(
          array(
            new IntegerNode('1'),
            new StringNode('one'),
          ),
          array(
            new IntegerNode('2'),
            new StringNode('two'),
          ),
          array(
            new IntegerNode('3'),
            new StringNode('three'),
          ),
        ),
        'type'          => null,
      ))), 
      $this->parse($syntax)
    );
  }
}
