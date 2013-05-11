<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests navigation operator
 *
 */
class NavigationOperatorTest extends ExecutionTest {
  
  /**
   * Test member access
   *
   */
  #[@test]
  public function member_access_on_null() {
    $this->assertNull($this->run('$i= null; return $i?.member;'));
  }

  /**
   * Test member access
   *
   */
  #[@test]
  public function member_access_on_self() {
    $this->assertTrue($this->run('$i= new self() { bool $member= true; }; return $i?.member;'));
  }

  /**
   * Test method call
   *
   */
  #[@test]
  public function method_call_on_null() {
    $this->assertNull($this->run('$i= null; return $i?.toString();'));
  }

  /**
   * Test method call
   *
   */
  #[@test]
  public function method_call_on_self() {
    $this->assertEquals('OK', $this->run('$i= new self() { string toString() { return "OK"; } }; return $i?.toString();'));
  }

  /**
   * Test method call
   *
   */
  #[@test]
  public function method_call_on_null_member() {
    $this->assertNull($this->run('$i= new self() { lang.types.Integer $member= null; }; return $i?.member?.intValue();'));
  }

  /**
   * Test method call
   *
   */
  #[@test]
  public function method_call_on_member() {
    $this->assertEquals(1, $this->run('$i= new self() { lang.types.Integer $member= new lang.types.Integer(1); }; return $i?.member?.intValue();'));
  }

  /**
   * Test invocation
   *
   */
  #[@test]
  public function invocation_on_null() {
    $this->assertNull($this->run('$i= null; return $i?.(true);'));
  }

  /**
   * Test member access
   *
   */
  #[@test]
  public function invocation_on_lambda() {
    $this->assertTrue($this->run('$i= #{ $a -> $a }; return $i?.(true);'));
  }
}
