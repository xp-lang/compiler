<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests MultiCatchs
 *
 */
class MultiCatchTest extends ExecutionTest {
  
  /**
   * Test catch
   *
   */
  #[@test]
  public function ioException() {
    $this->assertEquals('io.IOException', $this->run('
      try {
        throw new io.IOException("");
      } catch (io.IOException | rdbms.SQLException $e) {
        return $e.getClassName();
      }
      return null;
    '));
  }

  /**
   * Test catch
   *
   */
  #[@test]
  public function sqlException() {
    $this->assertEquals('rdbms.SQLException', $this->run('
      try {
        throw new rdbms.SQLException("");
      } catch (io.IOException | rdbms.SQLException $e) {
        return $e.getClassName();
      }
      return null;
    '));
  }

  /**
   * Test catch
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function iaException() {
    $this->run('
      try {
        throw new lang.IllegalArgumentException("");
      } catch (io.IOException | rdbms.SQLException $e) {
        return $e.getClassName();
      }
      return null;
    ');
  }
}