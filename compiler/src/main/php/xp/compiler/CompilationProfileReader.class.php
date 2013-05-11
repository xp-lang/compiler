<?php namespace xp\compiler;

use lang\XPClass;

/**
 * Reads compilation profiles from one or more property file
 *
 * @test    xp://net.xp_lang.tests.CompilationProfileReaderTest
 */
class CompilationProfileReader extends \lang\Object {
  protected $sources= array();
  
  /**
   * Add source properties
   *
   * @param   util.Properties source
   */
  public function addSource(\util\Properties $source) {
    $this->sources[]= $source;
  }
  
  /**
   * Get profile
   *
   * @return  xp.compiler.CompilationProfile
   */
  public function getProfile() {
    $profile= new CompilationProfile();
    foreach ($this->sources as $source) {
      foreach ($source->readArray('warnings', 'class') as $class) {
        $profile->addWarning(XPClass::forName($class)->newInstance());
      }
      foreach ($source->readArray('errors', 'class') as $class) {
        $profile->addError(XPClass::forName($class)->newInstance(), true);
      }
      foreach ($source->readArray('optimizations', 'class') as $class) {
        $profile->addOptimization(XPClass::forName($class)->newInstance());
      }
    }
    return $profile;
  }
}