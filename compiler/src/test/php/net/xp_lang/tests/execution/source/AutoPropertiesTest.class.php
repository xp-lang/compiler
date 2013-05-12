<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests automatic properties
 */
class AutoPropertiesTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Creates fixture. Note: cannot be done in an `@beforeClass` method 
   * since we need access to `$this->define()`
   */
  public function setUp() {
    parent::setUp();
    if (null === self::$fixture) {
      self::$fixture= $this->define('class', 'FixtureForAutoPropertiesTest', null, '{
        public int id { get; set; }
      }');
    }
  }

  /**
   * Test reading the id property
   */
  #[@test]
  public function initiallyNull() {
    $instance= self::$fixture->newInstance();
    $this->assertEquals(null, $instance->id);
  }

  /**
   * Test writing and reading the id property
   */
  #[@test]
  public function roundTrip() {
    $instance= self::$fixture->newInstance();
    $instance->id= 1;
    $this->assertEquals(1, $instance->id);
  }
}
