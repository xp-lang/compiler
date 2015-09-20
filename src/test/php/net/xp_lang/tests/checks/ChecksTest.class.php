<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\Checks;
use xp\compiler\types\MethodScope;
use xp\compiler\ast\StringNode;

class ChecksTest extends \unittest\TestCase {
  private static $check;
  private $fixture= null;
  private $scope= null;
  private $messages= [];

  #[@beforeClass]
  public static function defineChecks() {
    self::$check= newinstance('xp.compiler.checks.Check', [], '{
      public function node() { 
        return \lang\XPClass::forName("xp.compiler.ast.StringNode"); 
      }

      public function defer() { 
        return false; 
      }

      public function verify(\xp\compiler\ast\Node $in, \xp\compiler\types\Scope $scope) {
        return array("C100", "Test");
      }
    }');
  }

  /**
   * Sets up test case
   *
   * @return void
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

  #[@test]
  public function withoutCheck() {
    $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(), $this->messages);
  }

  #[@test]
  public function withErrorCheck() {
    $this->fixture->add(self::$check, true);
    $this->assertFalse($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(array('error', 'C100', 'Test')), $this->messages);
  }

  #[@test]
  public function withWarningCheck() {
    $this->fixture->add(self::$check, false);
    $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(array('warning', 'C100', 'Test')), $this->messages);
  }

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
  
  #[@test]
  public function clearChecks() {
    $this->fixture->add(self::$check, true);
    $this->fixture->clear();
    $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
    $this->assertEquals(array(), $this->messages);
  }
}
