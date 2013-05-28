<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests comparisons
 */
class ComparisonTest extends ExecutionTest {

  /**
   * Returns constants for use with constants tests
   */
  public function constants() {
    return array('0', 'null', 'false', 'true', '"string"', '[]', '[:]', '-1', '-0.5');
  }
  
  /**
   * Test constant == $a
   */
  #[@test, @values('constants')]
  public function constant_equality_on_lhs($constant) {
    $this->assertTrue(
      $this->run('$a= '.$constant.'; return '.$constant.' == $a;'), 
      $constant
    );
  }

  /**
   * Test constant === $a
   */
  #[@test, @values('constants')]
  public function constant_identity_on_lhs($constant) {
    $this->assertTrue(
      $this->run('$a= '.$constant.'; return '.$constant.' === $a;'), 
      $constant
    );
  }

  /**
   * Test $a == constant
   */
  #[@test, @values('constants')]
  public function constant_equality_on_rhs($constant) {
    $this->assertTrue(
      $this->run('$a= '.$constant.'; return $a == '.$constant.';'),
      $constant
    );
  }

  /**
   * Test $a === constant
   */
  #[@test, @values('constants')]
  public function constant_identity_on_rhs($constant) {
    $this->assertTrue(
      $this->run('$a= '.$constant.'; return $a === '.$constant.';'),
      $constant
    );
  }

  /**
   * Test <
   */
  #[@test]
  public function smallerThan() {
    $this->assertTrue($this->run('return 1 < 2;'), '1 < 2');
    $this->assertFalse($this->run('return 1 < 1;'), '1 < 1');
  }

  /**
   * Test <=
   */
  #[@test]
  public function smallerThanOrEqual() {
    $this->assertTrue($this->run('return 1 <= 2;'), '1 <= 2');
    $this->assertTrue($this->run('return 1 <= 1;'), '1 <= 1');
    $this->assertFalse($this->run('return 1 <= 0;'), '1 <= 0');
  }

  /**
   * Test <
   */
  #[@test]
  public function greaterThan() {
    $this->assertTrue($this->run('return 2 > 1;'), '2 > 1');
    $this->assertFalse($this->run('return 1 > 1;'), '1 > 1');
  }

  /**
   * Test >=
   */
  #[@test]
  public function greaterThanOrEqual() {
    $this->assertTrue($this->run('return 2 >= 1;'), '2 >= 1');
    $this->assertTrue($this->run('return 1 >= 1;'), '1 >= 1');
    $this->assertFalse($this->run('return 0 >= 1;'), '0 >= 1');
  }

  /**
   * Test !=
   */
  #[@test]
  public function notEqual() {
    $this->assertTrue($this->run('return 1 != 2;'), '1 != 2');
    $this->assertFalse($this->run('return 1 != 1;'), '1 != 1');
  }

  /**
   * Test !=
   */
  #[@test]
  public function isEqual() {
    $this->assertTrue($this->run('return 1 == 1;'), '1 == 1');
    $this->assertFalse($this->run('return 1 == 2;'), '1 == 2');
  }

  /**
   * Test !== with integers
   */
  #[@test]
  public function integersNotIdentical() {
    $this->assertTrue($this->run('return 1 !== 2;'), '1 !== 2');
    $this->assertFalse($this->run('return 1 !== 1;'), '1 !== 1');
  }

  /**
   * Test !== with integers
   */
  #[@test]
  public function integersIdentical() {
    $this->assertTrue($this->run('return 1 === 1;'), '1 === 1');
    $this->assertFalse($this->run('return 1 === 2;'), '1 === 2');
  }
}
