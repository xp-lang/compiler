<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.compilation';

  uses('net.xp_lang.tests.compilation.AnnotationsTest');

  /**
   * TestCase for method annotations
   *
   * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
   */
  class net·xp_lang·tests·compilation·MethodAnnotationsTest extends net·xp_lang·tests·compilation·AnnotationsTest {

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
        array('experimental' => NULL), 
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
      if (!XPClass::forName('lang.reflect.Parameter')->hasMethod('getAnnotations')) return;

      $type= $this->compile('class %s { [@$conn: inject(name= "db")] void fixture(rdbms.DBConnection $conn) { } }');
      $this->assertEquals(
        array('inject' => array('name' => 'db')), 
        this($type->getMethod('fixture')->getParameters(), 0)->getAnnotations()
      );
    }
  }
?>
