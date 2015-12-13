<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests operator overloading functionality at runtime
 */
class OperatorOverloadingTest extends ExecutionTest {
  
  #[@test]
  public function sprintf() {
    $this->assertEquals('Hello World', $this->run(
      '$s= new StringBuffer("Hello %s") % "World"; return $s.getBytes();',
      ['import net.xp_lang.tests.execution.source.StringBuffer;']
    ));
  }

  #[@test]
  public function concat_overloading() {
    $this->assertEquals('HelloWorld', $this->run(
      '$s= new StringBuffer("Hello") ~ "World"; return $s.getBytes();',
      ['import net.xp_lang.tests.execution.source.StringBuffer;']
    ));
  }

  #[@test]
  public function concat_qquals_overloading() {
    $this->assertEquals('HelloWorld', $this->run(
      '$s= new StringBuffer("Hello"); $s~= "World"; return $s.getBytes();',
      ['import net.xp_lang.tests.execution.source.StringBuffer;']
    ));
  }
}
