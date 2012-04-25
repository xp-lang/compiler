<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests properties
   *
   */
  class net�xp_lang�tests�execution�source�CompactSyntaxTest extends ExecutionTest {
    protected static $fixture= NULL;

    /**
     * Defines fixture class. Cannot be moved to a "beforeClass" method
     * because we need the compiler API instantiated.
     *
     */
    public function setUp() {
      parent::setUp();
      if (NULL !== self::$fixture) return;

      try {
        self::$fixture= $this->define('class', 'CompactSyntaxTestFixture', NULL, '{
          protected string $name = "Test";

          public string getName() -> $this.name;
          public void setName($this.name) { }
        }');
      } catch (Throwable $e) {
        throw new PrerequisitesNotMetError($e->getMessage(), $e);
      }
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
  }
?>
