<?php namespace net\xp_lang\tests;

use xp\compiler\emit\Buffer;

/**
 * Tests buffer class
 *
 * @see   xp://xp.compiler.emit.Buffer
 */
class BufferTest extends \unittest\TestCase {

  /**
   * Assertion helper
   *
   * @param  string source
   * @param  xp.compiler.emit.Buffer $buffer
   * @throws unittest.AssertionFailedError
   */
  private function assertSource($source, $buffer) {
    $this->assertEquals($source, (string)$buffer);
  }

  #[@test]
  public function can_create() {
    new Buffer();
  }

  #[@test]
  public function initially_empty() {
    $this->assertSource('', new Buffer());
  }

  #[@test]
  public function initial_value_set() {
    $this->assertSource('Test', new Buffer('Test'));
  }

  #[@test]
  public function line_one_default() {
    $this->assertEquals(1, (new Buffer())->line);
  }

  #[@test]
  public function line_number_passable() {
    $this->assertEquals(10, (new Buffer('', 10))->line);
  }

  #[@test]
  public function mark_when_empty() {
    $this->assertEquals(0, (new Buffer(''))->mark());
  }

  #[@test]
  public function mark() {
    $this->assertEquals(4, (new Buffer('Test'))->mark());
  }

  #[@test]
  public function position_plus_one_line_adds_space() {
    $b= new Buffer('One', 1);
    $b->position([2, 1]);
    $b->append('Two');
    $this->assertSource("One\nTwo", $b);
  }

  #[@test]
  public function position_plus_one_line_increases_line() {
    $b= new Buffer('One', 1);
    $b->position([2, 1]);
    $this->assertEquals(2, $b->line);
  }


  #[@test]
  public function position_plus_three_lines_adds_space() {
    $b= new Buffer('One', 1);
    $b->position([5, 1]);
    $b->append('Five');
    $this->assertSource("One\n\n\n\nFive", $b);
  }

  #[@test]
  public function position_plus_three_lines_increases_line() {
    $b= new Buffer('One', 1);
    $b->position([5, 1]);
    $this->assertEquals(5, $b->line);
  }

  #[@test]
  public function append_returns_this() {
    $b= new Buffer();
    $this->assertEquals($b, $b->append(''));
  }

  #[@test]
  public function append_source() {
    $b= new Buffer();
    $b->append('Test');
    $this->assertSource('Test', $b);
  }

  #[@test]
  public function append_source_with_newline() {
    $b= new Buffer();
    $b->append("One\nTwo");
    $this->assertSource("One\nTwo", $b);
  }

  #[@test]
  public function append_source_with_newline_increases_line() {
    $b= new Buffer();
    $b->append("One\nTwo");
    $this->assertEquals(2, $b->line);
  }

  #[@test]
  public function append_source_with_newlines_increases_line() {
    $b= new Buffer();
    $b->append("One\nTwo\nThree");
    $this->assertEquals(3, $b->line);
  }

  #[@test]
  public function insert_source() {
    $b= new Buffer();
    $b->insert('Test', 0);
    $this->assertSource('Test', $b);
  }

  #[@test]
  public function insert_source_with_newline() {
    $b= new Buffer();
    $b->insert("One\nTwo", 0);
    $this->assertSource("One\nTwo", $b);
  }

  #[@test]
  public function insert_source_with_newline_increases_line() {
    $b= new Buffer();
    $b->insert("One\nTwo", 0);
    $this->assertEquals(2, $b->line);
  }

  #[@test]
  public function insert_source_with_newlines_increases_line() {
    $b= new Buffer();
    $b->insert("One\nTwo\nThree", 0);
    $this->assertEquals(3, $b->line);
  }

  #[@test]
  public function insert_at_the_beginning() {
    $b= new Buffer('Test');
    $b->insert('--', 0);
    $this->assertSource('--Test', $b);
  }

  #[@test]
  public function insert_in_the_middle() {
    $b= new Buffer('Test');
    $b->insert('--', 2);
    $this->assertSource('Te--st', $b);
  }

  #[@test]
  public function insert_at_the_end() {
    $b= new Buffer('Test');
    $b->insert('--', 4);
    $this->assertSource('Test--', $b);
  }

  #[@test]
  public function insert_newline() {
    $b= new Buffer('Test');
    $b->insert("\n", 2);
    $this->assertSource("Te\nst", $b);
  }

  #[@test]
  public function insert_newline_increases_line() {
    $b= new Buffer('Test');
    $b->insert("\n", 2);
    $this->assertEquals(2, $b->line);
  }

  #[@test]
  public function replace() {
    $b= new Buffer('Teste');
    $b->replace('e', 'a');
    $this->assertSource('Tasta', $b);
  }

  #[@test]
  public function replace_returns_this() {
    $b= new Buffer('Teste');
    $this->assertEquals($b, $b->replace('e', 'a'));
  }
}