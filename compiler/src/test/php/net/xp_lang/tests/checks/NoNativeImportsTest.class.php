<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\NoNativeImports;
use xp\compiler\types\CompilationUnitScope;
use xp\compiler\ast\NativeImportNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.NoNativeImports
 */
class NoNativeImportsTest extends \unittest\TestCase {
  protected $fixture= null;
  protected $scope= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new NoNativeImports();
    $this->scope= new CompilationUnitScope();
  }
  
  /**
   * Test importing a function (namespace.function)
   *
   */
  #[@test]
  public function functionImport() {
    $this->assertEquals(
      array('N415', 'Native imports (pcre.preg_match) make code non-portable'), 
      $this->fixture->verify(new NativeImportNode(array('name' => 'pcre.preg_match')), $this->scope)
    );
  }

  /**
   * Test importing on demand (namespace.*) 
   *
   */
  #[@test]
  public function importOnDemand() {
    $this->assertEquals(
      array('N415', 'Native imports (pcre.*) make code non-portable'), 
      $this->fixture->verify(new NativeImportNode(array('name' => 'pcre.*')), $this->scope)
    );
  }
}