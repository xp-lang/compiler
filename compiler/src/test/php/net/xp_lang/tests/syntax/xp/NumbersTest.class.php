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

    /**
     * Test octal numbers
     *
     */
    #[@test]
    public function octal_zero() {
      $this->assertEquals(array(new IntegerNode('0')), $this->parse('00;'));
    }

    /**
     * Test octal numbers
     *
     */
    #[@test]
    public function octal_0000() {
      $this->assertEquals(array(new IntegerNode('0')), $this->parse('0000;'));
    }

    /**
     * Test octal numbers
     *
     */
    #[@test]
    public function octal_0777() {
      $this->assertEquals(array(new IntegerNode('511')), $this->parse('0777;'));
    }

    /**
     * Test octal numbers
     *
     * @see   http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/#numbers
     */
    #[@test, @expect(class = 'lang.FormatException', withMessage= '/Illegal octal/')]
    public function malformed_octal_09() {
      $this->parse('09');
    }

    /**
     * Test octal numbers
     *
     */
    #[@test, @expect(class = 'lang.FormatException', withMessage= '/Illegal octal/')]
    public function malformed_octal_00X() {
      $this->parse('00X');
    }

    /**
     * Test octal numbers
     *
     */
    #[@test, @expect(class = 'lang.FormatException', withMessage= '/Illegal octal/')]
    public function malformed_octal_01c() {
      $this->parse('01c');
    }

    /**
     * Test integer numbers
     *
     */
    #[@test]
    public function integer_zero() {
      $this->assertEquals(array(new IntegerNode('0')), $this->parse('0;'));
    }

    /**
     * Test integer numbers
     *
     */
    #[@test]
    public function integer_huge() {
      $this->assertEquals(array(new IntegerNode('58635272821786587286382824657568871098287278276543219876543')), $this->parse('58635272821786587286382824657568871098287278276543219876543;'));
    }

    /**
     * Test hex numbers
     *
     */
    #[@test]
    public function hex_zero() {
      $this->assertEquals(array(new HexNode('0x0')), $this->parse('0x0;'));
    }

    /**
     * Test hex numbers
     *
     */
    #[@test]
    public function hex_lowercase() {
      $this->assertEquals(array(new HexNode('0x61ae')), $this->parse('0x61ae;'));
    }

    /**
     * Test hex numbers
     *
     */
    #[@test]
    public function hex_uppercase() {
      $this->assertEquals(array(new HexNode('0X61AE')), $this->parse('0X61AE;'));
    }


    /**
     * Test hex numbers
     *
     */
    #[@test]
    public function hex_mixedcase() {
      $this->assertEquals(array(new HexNode('0xACe')), $this->parse('0xACe;'));
    }

    /**
     * Test decimal numbers
     *
     */
    #[@test]
    public function decimal_zero() {
      $this->assertEquals(array(new DecimalNode('0.0')), $this->parse('0.0;'));
    }

    /**
     * Test decimal numbers
     *
     */
    #[@test]
    public function decimal() {
      $this->assertEquals(array(new DecimalNode('6.100')), $this->parse('6.100;'));
    }

    /**
     * Test decimal numbers
     *
     */
    #[@test]
    public function decimal_exponent_lowercase() {
      $this->assertEquals(array(new DecimalNode('1e4')), $this->parse('1e4;'));
    }

    /**
     * Test decimal numbers
     *
     */
    #[@test]
    public function decimal_exponent_uppercase() {
      $this->assertEquals(array(new DecimalNode('1E4')), $this->parse('1E4;'));
    }

    /**
     * Test decimal numbers
     *
     */
    #[@test]
    public function decimal_fraction_exponent_uppercase() {
      $this->assertEquals(array(new DecimalNode('1.5e4')), $this->parse('1.5e4;'));
    }
  }
?>
