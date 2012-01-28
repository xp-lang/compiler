<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.compilation';

  uses('net.xp_lang.tests.compilation.AnnotationsTest');

  /**
   * TestCase for constructor annotations
   *
   * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
   */
  class net·xp_lang·tests·compilation·ConstructorAnnotationsTest extends net·xp_lang·tests·compilation·AnnotationsTest {

    /**
     * Test Method::getAnnotations() on compiled type
     *
     */
    #[@test]
    public function noAnnotations() {
      $this->assertEquals(array(), $this->compile('class %s { __construct() { } }')->getConstructor()->getAnnotations());
    }

    /**
     * Test Method::getAnnotations() on compiled type
     *
     */
    #[@test]
    public function simpleAnnotation() {
      $this->assertEquals(
        array('experimental' => NULL), 
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
        array('experimental' => 'beta'), 
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
        array('experimental' => array('stages' => array('beta', 'RC'))), 
        $this->compile('class %s { [@experimental(stages= ["beta", "RC"])] __construct() { } }')->getConstructor()->getAnnotations()
      );
    }
  }
?>
