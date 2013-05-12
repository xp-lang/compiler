<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests automatic properties
 */
class AutoPropertiesTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Creates fixture.
   */
  #[@beforeClass]
  public function setUp() {
    self::$fixture= self::define('class', 'FixtureForAutoPropertiesTest', null, '{
      public int id { get; set; }
    }');
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
