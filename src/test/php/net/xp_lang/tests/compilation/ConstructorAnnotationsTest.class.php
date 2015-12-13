<?php namespace net\xp_lang\tests\compilation;

/**
 * TestCase for constructor annotations
 *
 * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
 */
class ConstructorAnnotationsTest extends AnnotationsTest {

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function noAnnotations() {
    $this->assertEquals([], $this->compile('class %s { __construct() { } }')->getConstructor()->getAnnotations());
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function simpleAnnotation() {
    $this->assertEquals(
      ['experimental' => null], 
      $this->compile('class %s { [@experimental] __construct() { } }')->getConstructor()->getAnnotations()
    );
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithDefault() {
    $this->assertEquals(
      ['experimental' => 'beta'], 
      $this->compile('class %s { [@experimental("beta")] __construct() { } }')->getConstructor()->getAnnotations()
    );
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithParams() {
    $this->assertEquals(
      ['experimental' => ['stages' => ['beta', 'RC']]], 
      $this->compile('class %s { [@experimental(stages= ["beta", "RC"])] __construct() { } }')->getConstructor()->getAnnotations()
    );
  }
}
