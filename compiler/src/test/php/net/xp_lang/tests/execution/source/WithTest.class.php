<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests with statement
 *
 */
class WithTest extends ExecutionTest {
  
  /**
   * Test
   *
   */
  #[@test]
  public function oneAssignment() {
    $this->assertEquals('child', $this->run('with ($n= new xml.Node("root").addChild(new xml.Node("child"))) { 
      return $n.getName(); 
    }'));
  }
}