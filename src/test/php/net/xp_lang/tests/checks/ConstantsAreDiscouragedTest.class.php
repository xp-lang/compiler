<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\ConstantsAreDiscouraged;
use xp\compiler\types\MethodScope;
use xp\compiler\ast\ConstantNode;

class ConstantsAreDiscouragedTest extends \unittest\TestCase {
  private $fixture;
  private $scope;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new ConstantsAreDiscouraged();
    $this->scope= new MethodScope();
  }
  
  #[@test]
  public function constantsAreDiscouraged() {
    $this->assertEquals(
      array('T203', 'Global constants (DIRECTORY_SEPARATOR) are discouraged'), 
      $this->fixture->verify(new ConstantNode('DIRECTORY_SEPARATOR'), $this->scope)
    );
  }
}