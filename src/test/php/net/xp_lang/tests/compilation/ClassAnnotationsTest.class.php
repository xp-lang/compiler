<?php namespace net\xp_lang\tests\compilation;

/**
 * TestCase for class annotations
 *
 * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
 */
class ClassAnnotationsTest extends AnnotationsTest {

  /**
   * Test XPClass::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function noAnnotations() {
    $this->assertEquals([], $this->compile('class %s { }')->getAnnotations());
  }

  /**
   * Test XPClass::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function simpleAnnotation() {
    $this->assertEquals(
      ['experimental' => null], 
      $this->compile('[@experimental] class %s { }')->getAnnotations()
    );
  }

  /**
   * Test XPClass::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithDefault() {
    $this->assertEquals(
      ['experimental' => 'beta'], 
      $this->compile('[@experimental("beta")] class %s { }')->getAnnotations()
    );
  }

  /**
   * Test XPClass::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithParams() {
    $this->assertEquals(
      ['experimental' => ['stages' => ['beta', 'RC']]], 
      $this->compile('[@experimental(stages= ["beta", "RC"])] class %s { }')->getAnnotations()
    );
  }
}