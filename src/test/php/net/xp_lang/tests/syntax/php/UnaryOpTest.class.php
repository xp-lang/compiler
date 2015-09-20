<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;

class UnaryOpTest extends ParserTestCase {

  #[@test]
  public function negation() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new VariableNode('i'),
      'op'            => '!'
    ))), $this->parse('
      !$i;
    '));
  }

  #[@test]
  public function complement() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new VariableNode('i'),
      'op'            => '~'
    ))), $this->parse('
      ~$i;
    '));
  }

  #[@test]
  public function increment() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new VariableNode('i'),
      'op'            => '++'
    ))), $this->parse('
      ++$i;
    '));
  }

  
  #[@test]
  public function decrement() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new VariableNode('i'),
      'op'            => '--'
    ))), $this->parse('
      --$i;
    '));
  }
}