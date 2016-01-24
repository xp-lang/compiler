<?php namespace net\xp_lang\tests\execution\source;

use lang\ClassCastException;

/**
 * Tests casting
 *
 */
class CastingTest extends ExecutionTest {
  
  #[@test]
  public function integerToString() {
    $this->assertEquals('1', $this->run('return 1 as string;'));
  }

  #[@test]
  public function stringToInteger() {
    $this->assertEquals(1, $this->run('return "1" as int;'));
  }

  #[@test]
  public function integerToDouble() {
    $this->assertEquals(1.0, $this->run('return 1 as double;'));
  }

  #[@test]
  public function doubleToInteger() {
    $this->assertEquals(1, $this->run('return 1.0 as int;'));
  }

  #[@test]
  public function oneAsBoolean() {
    $this->assertTrue($this->run('return 1 as bool;'));
  }

  #[@test]
  public function zeroAsBoolean() {
    $this->assertFalse($this->run('return 0 as bool;'));
  }

  #[@test]
  public function nullAsBoolean() {
    $this->assertFalse($this->run('return null as bool;'));
  }

  #[@test]
  public function emptyStringAsBoolean() {
    $this->assertFalse($this->run('return "" as bool;'));
  }

  #[@test]
  public function stringAsBoolean() {
    $this->assertTrue($this->run('return "a" as bool;'));
  }

  #[@test]
  public function numericOneStringAsBoolean() {
    $this->assertTrue($this->run('return "1" as bool;'));
  }

  #[@test]
  public function numericZeroStringAsBoolean() {
    $this->assertFalse($this->run('return "0" as bool;'));
  }

  #[@test]
  public function zeroAsIntArray() {
    $this->assertEquals([0], $this->run('return 0 as int[];'));
  }

  #[@test]
  public function stringAsStringArray() {
    $this->assertEquals(['Hello'], $this->run('return "Hello" as string[];'));
  }

  #[@test]
  public function nullAsVarArray() {
    $this->assertEquals([], $this->run('return null as var[];'));
  }

  #[@test]
  public function dateAsValue() {
    $this->run('return new util.Date() as lang.Value;');
  }

  #[@test, @expect(ClassCastException::class)]
  public function objectAsDate() {
    $this->run('return new lang.Object() as util.Date;');
  }

  #[@test]
  public function unverifiedThisAsDate() {
    $this->run('return $this as util.Date?;');
  }

  #[@test, @expect(ClassCastException::class)]
  public function objectAsString() {
    $this->run('return new lang.Object() as string;');
  }

  #[@test]
  public function objectAsArray() {
    $this->run('return new lang.Object() as var[];');
  }

  #[@test]
  public function unverifiedStringAsStringArray() {
    $this->assertEquals('Hello', $this->run('return "Hello" as string[]?;'));
  }

  #[@test]
  public function unverifiedStringAsInt() {
    $this->assertEquals('1', $this->run('return "1" as int?;'));
  }
}
