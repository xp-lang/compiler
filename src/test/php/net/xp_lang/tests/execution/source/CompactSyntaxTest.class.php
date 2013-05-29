<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests compact syntax
 */
class CompactSyntaxTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Defines fixture class.
   */
  #[@beforeClass]
  public static function defineFixture() {
    self::$fixture= self::define('class', 'CompactSyntaxTestFixture', null, '{
      protected string $name = "Test";

      public string getName() -> $this.name;
      public void setName($this.name) { }
      public this withName($this.name) { }
      public this useName($this.name= "Default");
    }');
  }
  
  /**
   * Test getName()
   *
   */
  #[@test]
  public function getFixtureName() {
    $this->assertEquals('Test', self::$fixture->newInstance()->getName());
  }

  /**
   * Test setName()
   *
   */
  #[@test]
  public function setFixtureName() {
    $name= 'Roundtrip Test';
    $fixture= self::$fixture->newInstance();
    $fixture->setName($name);
    $this->assertEquals($name, $fixture->getName());
  }

  /**
   * Test withName()
   *
   */
  #[@test]
  public function withFixtureName() {
    $name= 'Roundtrip Test';
    $fixture= self::$fixture->newInstance();
    $this->assertEquals($fixture, $fixture->withName($name));
    $this->assertEquals($name, $fixture->getName());
  }

  /**
   * Test useName()
   *
   */
  #[@test]
  public function useFixtureNameDefaultOmitted() {
    $this->assertEquals('Default', self::$fixture->newInstance()->useName()->getName());
  }

  /**
   * Test useName()
   *
   */
  #[@test]
  public function useFixtureName() {
    $name= 'Roundtrip Test';
    $this->assertEquals($name, self::$fixture->newInstance()->useName($name)->getName());
  }
}
