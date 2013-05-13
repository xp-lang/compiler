<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests assigments
 */
class AssignmentTest extends ExecutionTest {

  /**
   * Sets up this test. Add IsAssignable check
   */
  #[@beforeClass]
  public static function useIsAssignableCheck() {
    self::check(new \xp\compiler\checks\IsAssignable(), true);
  }
  
  /**
   * Test assigning to a variable
   *
   */
  #[@test]
  public function assignToVariable() {
    $this->compile('$a= 1;');
  }

  /**
   * Test assigning to a member
   *
   */
  #[@test]
  public function assignToMember() {
    $this->compile('$this.i= 1;');
  }

  /**
   * Test assigning to a member
   *
   */
  #[@test]
  public function assignToStaticMember() {
    $this->compile('self::$id= 0;');
  }

  /**
   * Test assigning to an array offset
   *
   */
  #[@test]
  public function assignToArrayOffset() {
    $this->compile('$a= []; $a[0]= 1;');
  }

  /**
   * Test assigning to an array addition
   *
   */
  #[@test]
  public function assignToArrayAdd() {
    $this->compile('$a= []; $a[]= 1;');
  }

  /**
   * Test assigning to an array offset
   *
   */
  #[@test]
  public function assignToMemberArrayOffset() {
    $this->compile('$this.a= []; $this.a[0]= 1;');
  }

  /**
   * Test assigning to an array addition
   *
   */
  #[@test]
  public function assignToMemberArrayAdd() {
    $this->compile('$this.a= []; $this.a[]= 1;');
  }

  /**
   * Test assigning to a method call is not allowed
   *
   */
  #[@test]
  public function assignToMemberReturnedByMethod() {
    $this->compile('self::class.getMethod("equals").accessible= true;');
  }
  
  /**
   * Test assigning to a function call is not allowed
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function assignToFunction() {
    $this->compile('is()= 1;');
  }

  /**
   * Test assigning to a method call is not allowed
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function assignToMethod() {
    $this->compile('$this.equals()= 1;');
  }

  /**
   * Test assigning to class member call is not allowed
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function assignToClassMember() {
    $this->compile('self::class= true;');
  }

  /**
   * Test assigning to a method call is not allowed
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function assignToStaticMethod() {
    $this->compile('self::getInstance()= 1;');
  }

  /**
   * Test the following code:
   *
   * <code>
   *   $a && $b+= 1;
   * </code>
   */
  #[@test]
  public function conditionalAssignment() {
    $this->compile('$a && $b+= 1;');
  }

  /**
   * Test the following code:
   *
   * <code>
   *   $a && ($b+= 1);
   * </code>
   */
  #[@test]
  public function conditionalAssignmentWithBraces() {
    $this->compile('$a && ($b+= 1);');
  }
}
