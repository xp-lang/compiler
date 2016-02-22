<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests navigation operator `?.`
 */
class NavigationOperatorTest extends ExecutionTest {

  #[@test]
  public function member_access_on_null() {
    $this->assertNull($this->run('$i= null; return $i?.member;'));
  }

  #[@test]
  public function member_access_on_self() {
    $this->assertTrue($this->run('$i= new self() { bool $member= true; }; return $i?.member;'));
  }

  #[@test]
  public function method_call_on_null() {
    $this->assertNull($this->run('$i= null; return $i?.toString();'));
  }

  #[@test]
  public function method_call_on_self() {
    $this->assertEquals('OK', $this->run('$i= new self() { string toString() { return "OK"; } }; return $i?.toString();'));
  }

  #[@test]
  public function method_call_on_null_member() {
    $this->assertNull($this->run('$i= new self() { util.Bytes $member= null; }; return $i?.member?.size();'));
  }

  #[@test]
  public function method_call_on_member() {
    $this->assertEquals(1, $this->run('$i= new self() { util.Bytes $member= new util.Bytes([1]); }; return $i?.member?.size();'));
  }
}
