<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class NumbersTest extends ParserTestCase {
  
    /**
     * Test "1.a" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalDecimalCharAfterDot() {
      $this->parse('1.a');
    }

    /**
     * Test "1.-" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalDecimalMinusAfterDot() {
      $this->parse('0.-');
    }

    /**
     * Test "0xZ" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalHexZ() {
      $this->parse('0xZ');
    }

    /**
     * Test "0x" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalHexMissingAfterX() {
      $this->parse('0x');
    }

    /**
     * Test "0x0+1"
     *
     */
    #[@test]
    public function php_bug_61256_0x0_plus_1() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new HexNode('0x0'), 
          'rhs' => new IntegerNode('1'), 
          'op'  => '+'
        ))),
        $this->parse('0x0+1;')
      );
    }

    /**
     * Test "0x0-1"
     *
     */
    #[@test]
    public function php_bug_61256_0x0_minus_1() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new HexNode('0x0'), 
          'rhs' => new IntegerNode('1'), 
          'op'  => '-'
        ))),
        $this->parse('0x0-1;')
      );
    }

    /**
     * Test "0x00+2"
     *
     */
    #[@test]
    public function php_bug_61095_0x00_plus_2() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new HexNode('0x00'), 
          'rhs' => new IntegerNode('2'), 
          'op'  => '+'
        ))),
        $this->parse('0x00+2;')
      );
    }

    /**
     * Test "0x00+0x02"
     *
     */
    #[@test]
    public function php_bug_61095_0x00_plus_0x02() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new HexNode('0x00'), 
          'rhs' => new HexNode('0x02'), 
          'op'  => '+'
        ))),
        $this->parse('0x00+0x02;')
      );
    }

    /**
     * Test "0x0+ 2"
     *
     * @see   http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/#numbers
     */
    #[@test]
    public function hex_add_0x0_plus_2_with_space() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new HexNode('0x0'), 
          'rhs' => new IntegerNode('2'), 
          'op'  => '+'
        ))),
        $this->parse('0x0+ 2;')
      );
    }

    /**
     * Test "0x0+2"
     *
     * @see   http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/#numbers
     */
    #[@test]
    public function hex_add_0x0_plus_2() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new HexNode('0x0'), 
          'rhs' => new IntegerNode('2'), 
          'op'  => '+'
        ))),
        $this->parse('0x0+2;')
      );
    }
  }
?>
