<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\HexNode;
use xp\compiler\ast\OctalNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\DecimalNode;
use lang\FormatException;

/**
 * Testcase for number syntax
 *
 * @see   http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/#numbers
 */
class NumbersTest extends ParserTestCase {

  #[@test, @expect(FormatException::class)]
  public function illegalDecimalCharAfterDot() {
    $this->parse('1.a');
  }

  #[@test, @expect(FormatException::class)]
  public function illegalDecimalMinusAfterDot() {
    $this->parse('0.-');
  }

  #[@test, @expect(FormatException::class)]
  public function illegalHexZ() {
    $this->parse('0xZ');
  }

  #[@test, @expect(FormatException::class)]
  public function illegalHexMissingAfterX() {
    $this->parse('0x');
  }

  #[@test]
  public function php_bug_61256_0x0_plus_1() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new HexNode('0x0'), 
        'rhs' => new IntegerNode('1'), 
        'op'  => '+'
      ])],
      $this->parse('0x0+1;')
    );
  }

  #[@test]
  public function php_bug_61256_0x0_minus_1() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new HexNode('0x0'), 
        'rhs' => new IntegerNode('1'), 
        'op'  => '-'
      ])],
      $this->parse('0x0-1;')
    );
  }

  #[@test]
  public function php_bug_61095_0x00_plus_2() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new HexNode('0x00'), 
        'rhs' => new IntegerNode('2'), 
        'op'  => '+'
      ])],
      $this->parse('0x00+2;')
    );
  }

  #[@test]
  public function php_bug_61095_0x00_plus_0x02() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new HexNode('0x00'), 
        'rhs' => new HexNode('0x02'), 
        'op'  => '+'
      ])],
      $this->parse('0x00+0x02;')
    );
  }

  #[@test]
  public function hex_add_0x0_plus_2_with_space() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new HexNode('0x0'), 
        'rhs' => new IntegerNode('2'), 
        'op'  => '+'
      ])],
      $this->parse('0x0+ 2;')
    );
  }

  #[@test]
  public function hex_add_0x0_plus_2() {
    $this->assertEquals(
      [new BinaryOpNode([
        'lhs' => new HexNode('0x0'), 
        'rhs' => new IntegerNode('2'), 
        'op'  => '+'
      ])],
      $this->parse('0x0+2;')
    );
  }

  #[@test]
  public function octal_zero() {
    $this->assertEquals([new OctalNode('00')], $this->parse('00;'));
  }

  #[@test]
  public function octal_0000() {
    $this->assertEquals([new OctalNode('0000')], $this->parse('0000;'));
  }

  #[@test]
  public function octal_0777() {
    $this->assertEquals([new OctalNode('0777')], $this->parse('0777;'));
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal octal/')]
  public function malformed_octal_09() {
    $this->parse('09');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal octal/')]
  public function malformed_octal_00X() {
    $this->parse('00X');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal octal/')]
  public function malformed_octal_01c() {
    $this->parse('01c');
  }

  #[@test]
  public function integer_zero() {
    $this->assertEquals([new IntegerNode('0')], $this->parse('0;'));
  }

  #[@test]
  public function integer_huge() {
    $this->assertEquals([new IntegerNode('58635272821786587286382824657568871098287278276543219876543')], $this->parse('58635272821786587286382824657568871098287278276543219876543;'));
  }

  #[@test]
  public function hex_zero() {
    $this->assertEquals([new HexNode('0x0')], $this->parse('0x0;'));
  }

  #[@test]
  public function hex_lowercase() {
    $this->assertEquals([new HexNode('0x61ae')], $this->parse('0x61ae;'));
  }

  #[@test]
  public function hex_uppercase() {
    $this->assertEquals([new HexNode('0X61AE')], $this->parse('0X61AE;'));
  }


  #[@test]
  public function hex_mixedcase() {
    $this->assertEquals([new HexNode('0xACe')], $this->parse('0xACe;'));
  }

  #[@test]
  public function decimal_zero() {
    $this->assertEquals([new DecimalNode('0.0')], $this->parse('0.0;'));
  }

  #[@test]
  public function decimal() {
    $this->assertEquals([new DecimalNode('6.100')], $this->parse('6.100;'));
  }

  #[@test]
  public function decimal_exponent_lowercase() {
    $this->assertEquals([new DecimalNode('1e4')], $this->parse('1e4;'));
  }

  #[@test]
  public function decimal_exponent_uppercase() {
    $this->assertEquals([new DecimalNode('1E4')], $this->parse('1E4;'));
  }

  #[@test]
  public function decimal_fraction_exponent_uppercase() {
    $this->assertEquals([new DecimalNode('1.5e4')], $this->parse('1.5e4;'));
  }

  #[@test]
  public function decimal_exponent_plus() {
    $this->assertEquals([new DecimalNode('1e+4')], $this->parse('1e+4;'));
  }

  #[@test]
  public function decimal_exponent_minus() {
    $this->assertEquals([new DecimalNode('1e-4')], $this->parse('1e-4;'));
  }

  #[@test]
  public function decimal_fraction_exponent_plus() {
    $this->assertEquals([new DecimalNode('1.5e+4')], $this->parse('1.5e+4;'));
  }

  #[@test]
  public function decimal_fraction_exponent_minus() {
    $this->assertEquals([new DecimalNode('1.5e-4')], $this->parse('1.5e-4;'));
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal decimal/')]
  public function exponent_missing() {
    $this->parse('1E;');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal decimal/')]
  public function exponent_double() {
    $this->parse('1EE2;');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal decimal/')]
  public function exponent_missing_plus() {
    $this->parse('1E+;');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal decimal/')]
  public function exponent_missing_minus() {
    $this->parse('1E-;');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal decimal/')]
  public function exponent_fraction_missing() {
    $this->parse('1.5E;');
  }

  #[@test, @expect(class= FormatException::class, withMessage= '/Illegal decimal/')]
  public function exponent_fraction_double() {
    $this->parse('1.5EE2;');
  }
}
