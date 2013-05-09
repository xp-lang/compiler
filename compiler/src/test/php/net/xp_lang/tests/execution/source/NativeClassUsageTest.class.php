<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests using native classes
 *
 */
class NativeClassUsageTest extends ExecutionTest {

  /**
   * Test PHP's ReflectionClass
   *
   */
  #[@test]
  public function reflectionClass() {
    $this->assertEquals(
      'ReflectionClass', 
      $this->run('$r= new php.reflection.ReflectionClass("ReflectionClass"); return $r.getName();')
    );
  }
}
