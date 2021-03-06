<?php namespace net\xp_lang\tests;

use xp\compiler\CompilationProfileReader;
use util\Properties;
use io\streams\Streams;
use io\streams\MemoryInputStream;

/**
 * TestCase
 *
 * @see   xp://xp.compiler.CompilationProfileReader
 */
class CompilationProfileReaderTest extends \unittest\TestCase {
  private $fixture= null;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new CompilationProfileReader();
  }
  
  /**
   * Creates a new properties object from a string
   *
   * @param   string source
   * @return  util.Properties
   */
  private function newProperties($source) {
    $prop= new Properties(null);
    $prop->load(new MemoryInputStream(preg_replace('/^\s*/', '', $source)));
    return $prop;
  }

  #[@test]
  public function noWarningsMissingSection() {
    $this->fixture->addSource($this->newProperties('
    '));

    $this->assertEquals([], array_keys($this->fixture->getProfile()->warnings));
  }

  #[@test]
  public function noWarningsEmptySection() {
    $this->fixture->addSource($this->newProperties('
      [warnings]
    '));

    $this->assertEquals([], array_keys($this->fixture->getProfile()->warnings));
  }
  
  #[@test]
  public function oneWarning() {
    $this->fixture->addSource($this->newProperties('
      [warnings]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation'],
      array_keys($this->fixture->getProfile()->warnings)
    );
  }

  #[@test]
  public function twoWarnings() {
    $this->fixture->addSource($this->newProperties('
      [warnings]
      class[]=xp.compiler.checks.TypeHasDocumentation
      class[]=xp.compiler.checks.TypeMemberHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation', 'xp.compiler.checks.TypeMemberHasDocumentation'],
      array_keys($this->fixture->getProfile()->warnings)
    );
  }

  #[@test]
  public function twoWarningsViaTwoSources() {
    $this->fixture->addSource($this->newProperties('
      [warnings]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));
    $this->fixture->addSource($this->newProperties('
      [warnings]
      class[]=xp.compiler.checks.TypeMemberHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation', 'xp.compiler.checks.TypeMemberHasDocumentation'],
      array_keys($this->fixture->getProfile()->warnings)
    );
  }

  #[@test]
  public function sameWarningViaTwoSources() {
    $this->fixture->addSource($this->newProperties('
      [warnings]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));
    $this->fixture->addSource($this->newProperties('
      [warnings]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation'],
      array_keys($this->fixture->getProfile()->warnings)
    );
  }

  #[@test]
  public function noErrorsMissingSection() {
    $this->fixture->addSource($this->newProperties('
    '));

    $this->assertEquals([], array_keys($this->fixture->getProfile()->errors));
  }

  #[@test]
  public function noErrorsEmptySection() {
    $this->fixture->addSource($this->newProperties('
      [errors]
    '));

    $this->assertEquals([], array_keys($this->fixture->getProfile()->errors));
  }

  #[@test]
  public function oneError() {
    $this->fixture->addSource($this->newProperties('
      [errors]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation'],
      array_keys($this->fixture->getProfile()->errors)
    );
  }

  #[@test]
  public function twoErrors() {
    $this->fixture->addSource($this->newProperties('
      [errors]
      class[]=xp.compiler.checks.TypeHasDocumentation
      class[]=xp.compiler.checks.TypeMemberHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation', 'xp.compiler.checks.TypeMemberHasDocumentation'],
      array_keys($this->fixture->getProfile()->errors)
    );
  }

  #[@test]
  public function twoErrorsViaTwoSources() {
    $this->fixture->addSource($this->newProperties('
      [errors]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));
    $this->fixture->addSource($this->newProperties('
      [errors]
      class[]=xp.compiler.checks.TypeMemberHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation', 'xp.compiler.checks.TypeMemberHasDocumentation'],
      array_keys($this->fixture->getProfile()->errors)
    );
  }

  #[@test]
  public function sameErrorViaTwoSources() {
    $this->fixture->addSource($this->newProperties('
      [errors]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));
    $this->fixture->addSource($this->newProperties('
      [errors]
      class[]=xp.compiler.checks.TypeHasDocumentation
    '));

    $this->assertEquals(
      ['xp.compiler.checks.TypeHasDocumentation'],
      array_keys($this->fixture->getProfile()->errors)
    );
  }

  #[@test]
  public function noOptimizationsMissingSection() {
    $this->fixture->addSource($this->newProperties('
    '));

    $this->assertEquals([], array_keys($this->fixture->getProfile()->optimizations));
  }

  #[@test]
  public function noOptimizationsEmptySection() {
    $this->fixture->addSource($this->newProperties('
      [optimizations]
    '));

    $this->assertEquals([], array_keys($this->fixture->getProfile()->optimizations));
  }

  #[@test]
  public function oneOptimization() {
    $this->fixture->addSource($this->newProperties('
      [optimizations]
      class[]=xp.compiler.optimize.BinaryOptimization
    '));

    $this->assertEquals(
      ['xp.compiler.optimize.BinaryOptimization'],
      array_keys($this->fixture->getProfile()->optimizations)
    );
  }

  #[@test]
  public function twoOptimizations() {
    $this->fixture->addSource($this->newProperties('
      [optimizations]
      class[]=xp.compiler.optimize.BinaryOptimization
      class[]=xp.compiler.optimize.DeadCodeElimination
    '));

    $this->assertEquals(
      ['xp.compiler.optimize.BinaryOptimization', 'xp.compiler.optimize.DeadCodeElimination'],
      array_keys($this->fixture->getProfile()->optimizations)
    );
  }

  #[@test]
  public function twoOptimizationsViaTwoSources() {
    $this->fixture->addSource($this->newProperties('
      [optimizations]
      class[]=xp.compiler.optimize.BinaryOptimization
    '));
    $this->fixture->addSource($this->newProperties('
      [optimizations]
      class[]=xp.compiler.optimize.DeadCodeElimination
    '));

    $this->assertEquals(
      ['xp.compiler.optimize.BinaryOptimization', 'xp.compiler.optimize.DeadCodeElimination'],
      array_keys($this->fixture->getProfile()->optimizations)
    );
  }

  #[@test]
  public function sameOptimizationViaTwoSources() {
    $this->fixture->addSource($this->newProperties('
      [optimizations]
      class[]=xp.compiler.optimize.BinaryOptimization
    '));
    $this->fixture->addSource($this->newProperties('
      [optimizations]
      class[]=xp.compiler.optimize.BinaryOptimization
    '));

    $this->assertEquals(
      ['xp.compiler.optimize.BinaryOptimization'],
      array_keys($this->fixture->getProfile()->optimizations)
    );
  }
}