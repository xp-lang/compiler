<?php namespace net\xp_lang\tests\compilation;

/**
 * TestCase for field annotations
 *
 * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
 */
class FieldAnnotationsTest extends AnnotationsTest {

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function noAnnotations() {
    $this->assertEquals(['type' => 'var'], $this->compile('class %s { var $fixture; }')->getField('fixture')->getAnnotations());
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function simpleAnnotation() {
    $this->assertEquals(
      ['type' => 'var', 'experimental' => null], 
      $this->compile('class %s { [@experimental] var $fixture; }')->getField('fixture')->getAnnotations()
    );
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithDefault() {
    $this->assertEquals(
      ['type' => 'var', 'experimental' => 'beta'], 
      $this->compile('class %s { [@experimental("beta")] var $fixture; }')->getField('fixture')->getAnnotations()
    );
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithParams() {
    $this->assertEquals(
      ['type' => 'var', 'experimental' => ['stages' => ['beta', 'RC']]], 
      $this->compile('class %s { [@experimental(stages= ["beta", "RC"])] var $fixture; }')->getField('fixture')->getAnnotations()
    );
  }
}