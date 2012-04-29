<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'xp.compiler.emit.Buffer');

  /**
   * Tests buffer class
   *
   * @see   xp://xp.compiler.emit.Buffer
   */
  class BufferTest extends TestCase {
  
    /**
     * Assertion helper
     *
     * @param   string source
     * @param   sxp.compiler.emit.Buffer buffer
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSource($source, $buffer) {
      $this->assertEquals($source, (string)$buffer);
    }
  
    /**
     * Test constructor
     *
     */
    #[@test]
    public function can_create() {
      new xp·compiler·emit·Buffer();
    }

    /**
     * Test constructor
     *
     */
    #[@test]
    public function initially_empty() {
      $this->assertSource('', new xp·compiler·emit·Buffer());
    }

    /**
     * Test constructor
     *
     */
    #[@test]
    public function initial_value_set() {
      $this->assertSource('Test', new xp·compiler·emit·Buffer('Test'));
    }

    /**
     * Test constructor
     *
     */
    #[@test]
    public function line_one_default() {
      $this->assertEquals(1, create(new xp·compiler·emit·Buffer())->line);
    }

    /**
     * Test constructor
     *
     */
    #[@test]
    public function line_number_passable() {
      $this->assertEquals(10, create(new xp·compiler·emit·Buffer('', 10))->line);
    }

    /**
     * Test mark()
     *
     */
    #[@test]
    public function mark_when_empty() {
      $this->assertEquals(0, create(new xp·compiler·emit·Buffer(''))->mark());
    }

    /**
     * Test mark()
     *
     */
    #[@test]
    public function mark() {
      $this->assertEquals(4, create(new xp·compiler·emit·Buffer('Test'))->mark());
    }

    /**
     * Test position() and source
     *
     */
    #[@test]
    public function position_plus_one_line_adds_space() {
      $b= new xp·compiler·emit·Buffer('One', 1);
      $b->position(array(2, 1));
      $b->append('Two');
      $this->assertSource("One\nTwo", $b);
    }

    /**
     * Test position() and line
     *
     */
    #[@test]
    public function position_plus_one_line_increases_line() {
      $b= new xp·compiler·emit·Buffer('One', 1);
      $b->position(array(2, 1));
      $this->assertEquals(2, $b->line);
    }


    /**
     * Test position() and source
     *
     */
    #[@test]
    public function position_plus_three_lines_adds_space() {
      $b= new xp·compiler·emit·Buffer('One', 1);
      $b->position(array(5, 1));
      $b->append('Five');
      $this->assertSource("One\n\n\n\nFive", $b);
    }

    /**
     * Test position() and line
     *
     */
    #[@test]
    public function position_plus_three_lines_increases_line() {
      $b= new xp·compiler·emit·Buffer('One', 1);
      $b->position(array(5, 1));
      $this->assertEquals(5, $b->line);
    }

    /**
     * Test append()
     *
     */
    #[@test]
    public function append_returns_this() {
      $b= new xp·compiler·emit·Buffer();
      $this->assertEquals($b, $b->append(''));
    }

    /**
     * Test append()
     *
     */
    #[@test]
    public function append_source() {
      $b= new xp·compiler·emit·Buffer();
      $b->append('Test');
      $this->assertSource('Test', $b);
    }

    /**
     * Test append()
     *
     */
    #[@test]
    public function append_source_with_newline() {
      $b= new xp·compiler·emit·Buffer();
      $b->append("One\nTwo");
      $this->assertSource("One\nTwo", $b);
    }

    /**
     * Test append()
     *
     */
    #[@test]
    public function append_source_with_newline_increases_line() {
      $b= new xp·compiler·emit·Buffer();
      $b->append("One\nTwo");
      $this->assertEquals(2, $b->line);
    }

    /**
     * Test append()
     *
     */
    #[@test]
    public function append_source_with_newlines_increases_line() {
      $b= new xp·compiler·emit·Buffer();
      $b->append("One\nTwo\nThree");
      $this->assertEquals(3, $b->line);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_source() {
      $b= new xp·compiler·emit·Buffer();
      $b->insert('Test', 0);
      $this->assertSource('Test', $b);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_source_with_newline() {
      $b= new xp·compiler·emit·Buffer();
      $b->insert("One\nTwo", 0);
      $this->assertSource("One\nTwo", $b);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_source_with_newline_increases_line() {
      $b= new xp·compiler·emit·Buffer();
      $b->insert("One\nTwo", 0);
      $this->assertEquals(2, $b->line);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_source_with_newlines_increases_line() {
      $b= new xp·compiler·emit·Buffer();
      $b->insert("One\nTwo\nThree", 0);
      $this->assertEquals(3, $b->line);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_at_the_beginning() {
      $b= new xp·compiler·emit·Buffer('Test');
      $b->insert('--', 0);
      $this->assertSource('--Test', $b);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_in_the_middle() {
      $b= new xp·compiler·emit·Buffer('Test');
      $b->insert('--', 2);
      $this->assertSource('Te--st', $b);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_at_the_end() {
      $b= new xp·compiler·emit·Buffer('Test');
      $b->insert('--', 4);
      $this->assertSource('Test--', $b);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_newline() {
      $b= new xp·compiler·emit·Buffer('Test');
      $b->insert("\n", 2);
      $this->assertSource("Te\nst", $b);
    }

    /**
     * Test insert()
     *
     */
    #[@test]
    public function insert_newline_increases_line() {
      $b= new xp·compiler·emit·Buffer('Test');
      $b->insert("\n", 2);
      $this->assertEquals(2, $b->line);
    }

    /**
     * Test replace()
     *
     */
    #[@test]
    public function replace() {
      $b= new xp·compiler·emit·Buffer('Teste');
      $b->replace('e', 'a');
      $this->assertSource('Tasta', $b);
    }

    /**
     * Test replace()
     *
     */
    #[@test]
    public function replace_returns_this() {
      $b= new xp·compiler·emit·Buffer('Teste');
      $this->assertEquals($b, $b->replace('e', 'a'));
    }
  }
?>
