<?php namespace net\xp_lang\tests\execution\source;

use lang\ClassLoader;
use net\xp_lang\tests\StringBuffer;

/**
 * Tests chaining
 */
class ChainingTest extends ExecutionTest {

  #[@test]
  public function parentOfTestClass() {
    $this->assertEquals(
      'lang.Object', 
      $this->run('return $this.getClass().getParentClass().getName();')
    );
  }

  #[@test]
  public function firstMethodOfTestClass() {
    $this->assertEquals(
      'run', 
      $this->run('return $this.getClass().getMethods()[0].getName();')
    );
  }

  #[@test]
  public function methodCallAfterNewObject() {
    $this->assertEquals(
      false, 
      $this->run('return new Object().equals($this);')
    );
  }

  #[@test]
  public function chainedMethodCallAfterNewObject() {
    $this->assertEquals(
      'lang.Object', 
      $this->run('return new Object().getClass().getName();')
    );
  }

  #[@test]
  public function chainedNestedMethodCallAfterNewObject() {
    $this->assertEquals(
      new StringBuffer('Test'), 
      $this->run('return new net.xp_lang.tests.StringBuffer().append("Test");')
    );
  }

  #[@test]
  public function arrayAccessAfterNew() {
    $this->assertEquals(
      'e',
      $this->run('return new net.xp_lang.tests.StringBuffer("Test")[1];')
    );
  }

  #[@test]
  public function arrayAccessAfterStaticMethod() {
    $this->assertEquals(
      'e',
      $this->run('return net.xp_lang.tests.StringBuffer::valueOf("Test")[1];')
    );
  }

  #[@test]
  public function arrayAccessAfterNewTypedArray() {
    $this->assertEquals(
      6,
      $this->run('return new int[]{5, 6, 7}[1];')
    );
  }

  #[@test]
  public function arrayAccessAfterNewUntypedArray() {
    $this->assertEquals(
      6,
      $this->run('return [5, 6, 7][1];')
    );
  }

  #[@test]
  public function memberAfterNewTypedArray() {
    $this->assertEquals(
      1, 
      $this->run('return new string[]{"Hello"}.length;')
    );
  }

  #[@test]
  public function memberAfterNewUntypedArray() {
    $this->assertEquals(
      1, 
      $this->run('return ["Hello"].length;')
    );
  }

  #[@test]
  public function arrayOfArrays() {
    $this->assertEquals(
      4, 
      $this->run('$a= [[1, 2], [3, 4]]; return $a[1][1];')
    );
  }

  #[@test]
  public function staticMemberArrayAccess() {
    $this->assertFalse($this->run('return isset(xp::$errors[__FILE__]);'));
  }

  #[@test]
  public function afterBracedExpression() {
    $this->assertEquals(4, $this->run('return (1 ? new net.xp_lang.tests.StringBuffer("Test") : null).length;'));
  }
}