<?php namespace net\xp_lang\tests\resolve;

use xp\compiler\ast\DecimalNode;
use xp\compiler\ast\HexNode;
use xp\compiler\ast\OctalNode;

/**
 * TestCase
 *
 * @see   xp://xp.compiler.ast.DecimalNode
 * @see   xp://xp.compiler.ast.HexNode
 * @see   xp://xp.compiler.ast.OctalNode
 */
class NumberResolveTest extends \unittest\TestCase {

  /**
   * Test [N]e[N]
   */
  #[@test]
  public function exponents() {
    foreach (array('1e4', '1E4', '1.0e4', '1.0E4') as $value) {
      $this->assertEquals(10000.0, create(new DecimalNode($value))->resolve(), $value);
    }
  }

  /**
   * Test [N]e+[N]
   */
  #[@test]
  public function exponents_with_plus() {
    foreach (array('1e+4', '1E+4', '1.0e+4', '1.0E+4') as $value) {
      $this->assertEquals(10000.0, create(new DecimalNode($value))->resolve(), $value);
    }
  }

  /**
   * Test [N]e-[N]
   */
  #[@test]
  public function exponents_with_minus() {
    foreach (array('1e-4', '1E-4', '1.0e-4', '1.0E-4') as $value) {
      $this->assertEquals(0.0001, create(new DecimalNode($value))->resolve(), $value);
    }
  }

  /**
   * Test hex
   */
  #[@test]
  public function hex_uppercase() {
    $this->assertEquals(57005, create(new HexNode('0XDEAD'))->resolve());
  }

  /**
   * Test hex
   */
  #[@test]
  public function hex_lowercase() {
    $this->assertEquals(57005, create(new HexNode('0xdead'))->resolve());
  }

  /**
   * Test hex
   */
  #[@test]
  public function hex_mixedcase() {
    $this->assertEquals(57005, create(new HexNode('0xDeAd'))->resolve());
  }

  /**
   * Test octal
   */
  #[@test]
  public function octal() {
    $this->assertEquals(511, create(new OctalNode('0777'))->resolve());
  }
}