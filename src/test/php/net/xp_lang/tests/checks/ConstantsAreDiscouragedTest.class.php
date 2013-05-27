<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\ConstantsAreDiscouraged;
use xp\compiler\types\MethodScope;
use xp\compiler\ast\ConstantNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.ConstantsAreDiscouraged
 */
class ConstantsAreDiscouragedTest extends \unittest\TestCase {
  protected $fixture= null;
  protected $scope= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new ConstantsAreDiscouraged();
    $this->scope= new MethodScope();
  }
  
  /**
   * Test constants 
   *
   */
  #[@test]
  public function constantsAreDiscouraged() {
    $this->assertEquals(
      array('T203', 'Global constants (DIRECTORY_SEPARATOR) are discouraged'), 
      $this->fixture->verify(new ConstantNode('DIRECTORY_SEPARATOR'), $this->scope)
    );
  }
}