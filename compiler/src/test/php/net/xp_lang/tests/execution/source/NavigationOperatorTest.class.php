<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests navigation operator
   *
   */
  class net·xp_lang·tests·execution·source·NavigationOperatorTest extends ExecutionTest {
    
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
  }
?>
