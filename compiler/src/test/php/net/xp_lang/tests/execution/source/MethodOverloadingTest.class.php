<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests properties
 *
 */
class MethodOverloadingTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Sets up test case
   */
  #[@beforeClass]
  public static function defineFixture() {
    throw new \unittest\PrerequisitesNotMetError('Not yet implemented');

    self::$fixture= self::define('class', 'FixtureForMethodOverloadingTest', null, '{
      public bool compare(string $a, string $b) {
        return strcmp($a, $b);
      }
      
      public bool compare(int $a, int $b) {
        return $a === $b ? 0 : ($a < $b ? -1 : 1);
      }
      
      public bool run(string $which) {
        switch ($which) {
          case "strings": return $this.compare("Hello", "World");
          case "ints": return $this.compare(1, 2);
        }
      }
    }', array(
      'import native core.strcmp;',
    ));
  }
  
  /**
   * Test comparing strings
   *
   */
  #[@test]
  public function strings() {
    $this->assertEquals(-1, self::$fixture->newInstance()->run('strings'));
  }

  /**
   * Test comparing ints
   *
   */
  #[@test]
  public function ints() {
    $this->assertEquals(-1, self::$fixture->newInstance()->run('ints'));
  }
}
