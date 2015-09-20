<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\NoNativeImports;
use xp\compiler\types\CompilationUnitScope;
use xp\compiler\ast\NativeImportNode;

class NoNativeImportsTest extends \unittest\TestCase {
  private $fixture;
  private $scope;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new NoNativeImports();
    $this->scope= new CompilationUnitScope();
  }
  
  #[@test]
  public function functionImport() {
    $this->assertEquals(
      array('N415', 'Native imports (pcre.preg_match) make code non-portable'), 
      $this->fixture->verify(new NativeImportNode(array('name' => 'pcre.preg_match')), $this->scope)
    );
  }

  #[@test]
  public function importOnDemand() {
    $this->assertEquals(
      array('N415', 'Native imports (pcre.*) make code non-portable'), 
      $this->fixture->verify(new NativeImportNode(array('name' => 'pcre.*')), $this->scope)
    );
  }
}