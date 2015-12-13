<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\VariableNode;

/**
 * TestCase
 *
 */
class UnaryOpTest extends ParserTestCase {

  /**
   * Test negation
   *
   */
  #[@test]
  public function negation() {
    $this->assertEquals([new UnaryOpNode([
      'expression'    => new VariableNode('i'),
      'op'            => '!'
    ])], $this->parse('
      !$i;
    '));
  }

  /**
   * Test complement
   *
   */
  #[@test]
  public function complement() {
    $this->assertEquals([new UnaryOpNode([
      'expression'    => new VariableNode('i'),
      'op'            => '~'
    ])], $this->parse('
      ~$i;
    '));
  }

  /**
   * Test increment
   *
   */
  #[@test]
  public function increment() {
    $this->assertEquals([new UnaryOpNode([
      'expression'    => new VariableNode('i'),
      'op'            => '++'
    ])], $this->parse('
      ++$i;
    '));
  }

  /**
   * Test decrement
   *
   */
  #[@test]
  public function decrement() {
    $this->assertEquals([new UnaryOpNode([
      'expression'    => new VariableNode('i'),
      'op'            => '--'
    ])], $this->parse('
      --$i;
    '));
  }
}
