<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests properties
 *
 */
class MethodOverloadingTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    throw new \unittest\PrerequisitesNotMetError('Not yet implemented');

    parent::setUp();
    if (null !== self::$fixture) return;

    try {
      self::$fixture= $this->define('class', 'FixtureForMethodOverloadingTest', null, '{
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
    } catch (Throwable $e) {
      throw new PrerequisitesNotMetError($e->getMessage(), $e);
    }
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
