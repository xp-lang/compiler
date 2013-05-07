<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\Checks;
use xp\compiler\types\MethodScope;
use xp\compiler\ast\StringNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.Checks
 */
class ChecksTest extends \unittest\TestCase {
  protected static $check;
  protected $fixture= null;
  protected $scope= null;
  protected $messages= array();

  static function __static() {
    self::$check= newinstance('xp.compiler.checks.Check', array(), '{
      public function node() { 
        return XPClass::forName("xp.compiler.ast.StringNode"); 
      }

      public function defer() { 
        return false; 
      }

      public function verify(xp·compiler·ast·Node $in, Scope $scope) {
        return array("C100", "Test");
      }
    }');
  }

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new Checks();
    $this->scope= new MethodScope();
  }
  
  /**
   * Callback for warnings
   *
   * @param   string code
   * @param   string message
   */
  public function warn($code, $message) {
    $this->messages[]= array('warning', $code, $message);
  }

  /**
   * Callback for errors
   *
   * @param   string code
   * @param   string message
   */
  public function error($code, $message) {
    $this->messages[]= array('error', $code, $message);
  }

  /**
   * Test verify()
   *
   */
  #[@test]
  public function withoutCheck() {
    $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(), $this->messages);
  }

  /**
   * Test add() and verify()
   *
   */
  #[@test]
  public function withErrorCheck() {
    $this->fixture->add(self::$check, true);
    $this->assertFalse($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(array('error', 'C100', 'Test')), $this->messages);
  }

  /**
   * Test add() and verify()
   *
   */
  #[@test]
  public function withWarningCheck() {
    $this->fixture->add(self::$check, false);
    $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(array('warning', 'C100', 'Test')), $this->messages);
  }

  /**
   * Test add() and verify()
   *
   */
  #[@test]
  public function withWarningAndErrorChecks() {
    $this->fixture->add(self::$check, false);
    $this->fixture->add(self::$check, true);
    $this->assertFalse($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(
      array(array('warning', 'C100', 'Test'), array('error', 'C100', 'Test')), 
      $this->messages
    );
  }

  /**
   * Test add() and verify()
   *
   */
  #[@test]
  public function withTwoWarningChecks() {
    $this->fixture->add(self::$check, false);
    $this->fixture->add(self::$check, false);
    $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(
      array(array('warning', 'C100', 'Test'), array('warning', 'C100', 'Test')), 
      $this->messages
    );
  }

  /**
   * Test add() and verify()
   *
   */
  #[@test]
  public function withTwoErrorsChecks() {
    $this->fixture->add(self::$check, true);
    $this->fixture->add(self::$check, true);
    $this->assertFalse($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(
      array(array('error', 'C100', 'Test'), array('error', 'C100', 'Test')), 
      $this->messages
    );
  }
  
  /**
   * Test clear() and verify()
   *
   */
  #[@test]
  public function clearChecks() {
    $this->fixture->add(self::$check, true);
    $this->fixture->clear();
    $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(), $this->messages);
  }
}
