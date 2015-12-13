<?php namespace net\xp_lang\tests\compilation;

/**
 * TestCase for method annotations
 *
 * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
 */
class OperatorAnnotationsTest extends AnnotationsTest {

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function noAnnotations() {
    $this->assertEquals([], $this->compile('class %s { static self operator +(self $a, self $b) { } }')->getMethod('operator··plus')->getAnnotations());
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function simpleAnnotation() {
    $this->assertEquals(
      ['experimental' => null], 
      $this->compile('class %s { [@experimental] static self operator +(self $a, self $b) { } }')->getMethod('operator··plus')->getAnnotations()
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
      $this->compile('class %s { [@experimental("beta")] static self operator +(self $a, self $b) { } }')->getMethod('operator··plus')->getAnnotations()
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
      $this->compile('class %s { [@experimental(stages= ["beta", "RC"])] static self operator +(self $a, self $b) { } }')->getMethod('operator··plus')->getAnnotations()
    );
  }
}