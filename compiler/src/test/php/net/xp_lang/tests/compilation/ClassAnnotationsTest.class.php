<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.compilation';

  uses('net.xp_lang.tests.compilation.AnnotationsTest');

  /**
   * TestCase for class annotations
   *
   * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
   */
  class net·xp_lang·tests·compilation·ClassAnnotationsTest extends net·xp_lang·tests·compilation·AnnotationsTest {

    /**
     * Test XPClass::getAnnotations() on compiled type
     *
     */
    #[@test]
    public function noAnnotations() {
      $this->assertEquals(array(), $this->compile('class %s { }')->getAnnotations());
    }

    /**
     * Test XPClass::getAnnotations() on compiled type
     *
     */
    #[@test]
    public function simpleAnnotation() {
      $this->assertEquals(
        array('experimental' => NULL), 
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
        array('experimental' => 'beta'), 
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
        array('experimental' => array('stages' => array('beta', 'RC'))), 
        $this->compile('[@experimental(stages= ["beta", "RC"])] class %s { }')->getAnnotations()
      );
    }
  }
?>
