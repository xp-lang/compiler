<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests arrays
 *
 */
class ArrayTest extends ExecutionTest {
  
  /**
   * Test [1, 2]
   *
   */
  #[@test]
  public function untypedArray() {
    $this->assertEquals([1, 2], $this->run('return [1, 2];'));
  }

  /**
   * Test new string[] { "Hello", "World" }
   *
   */
  #[@test]
  public function typedArray() {
    $this->assertEquals(['Hello', 'World'], $this->run('return new string[] { "Hello", "World" };'));
  }

  /**
   * Test [1, 2].length
   *
   */
  #[@test]
  public function arrayLength() {
    $this->assertEquals(2, $this->run('return [1, 2].length;'));
  }
}