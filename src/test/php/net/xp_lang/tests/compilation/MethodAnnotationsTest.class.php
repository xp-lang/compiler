<?php namespace net\xp_lang\tests\compilation;

/**
 * TestCase for method annotations
 *
 * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
 */
class MethodAnnotationsTest extends AnnotationsTest {

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function noAnnotations() {
    $this->assertEquals(array(), $this->compile('class %s { void fixture() { } }')->getMethod('fixture')->getAnnotations());
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function simpleAnnotation() {
    $this->assertEquals(
      array('experimental' => null), 
      $this->compile('class %s { [@experimental] void fixture() { } }')->getMethod('fixture')->getAnnotations()
    );
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithDefault() {
    $this->assertEquals(
      array('experimental' => 'beta'), 
      $this->compile('class %s { [@experimental("beta")] void fixture() { } }')->getMethod('fixture')->getAnnotations()
    );
  }

  /**
   * Test Method::getAnnotations() on compiled type
   *
   */
  #[@test]
  public function annotationWithParams() {
    $this->assertEquals(
      array('experimental' => array('stages' => array('beta', 'RC'))), 
      $this->compile('class %s { [@experimental(stages= ["beta", "RC"])] void fixture() { } }')->getMethod('fixture')->getAnnotations()
    );
  }

  /**
   * Test parameter annotation
   *
   * @see   https://github.com/xp-framework/rfc/issues/218
   */
  #[@test]
  public function parameterAnnotation() {
    $type= $this->compile('class %s { [@$conn: inject(name= "db")] void fixture(var $conn) { } }');
    $this->assertEquals(
      array('inject' => array('name' => 'db')), 
      $type->getMethod('fixture')->getParameters()[0]->getAnnotations()
    );
  }
}