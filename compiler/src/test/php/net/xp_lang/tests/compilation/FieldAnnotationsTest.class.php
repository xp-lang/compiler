<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.compilation';

  uses('net.xp_lang.tests.compilation.AnnotationsTest');

  /**
   * TestCase for field annotations
   *
   * @see   xp://net.xp_lang.tests.compilation.AnnotationsTest
   */
  class net·xp_lang·tests·compilation·FieldAnnotationsTest extends net·xp_lang·tests·compilation·AnnotationsTest {

    /**
     * Test Method::getAnnotations() on compiled type
     *
     */
    #[@test]
    public function noAnnotations() {
      $this->assertEquals(array('type' => 'var'), $this->compile('class %s { var $fixture; }')->getField('fixture')->getAnnotations());
    }

    /**
     * Test Method::getAnnotations() on compiled type
     *
     */
    #[@test]
    public function simpleAnnotation() {
      $this->assertEquals(
        array('type' => 'var', 'experimental' => NULL), 
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
        array('type' => 'var', 'experimental' => 'beta'), 
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
        array('type' => 'var', 'experimental' => array('stages' => array('beta', 'RC'))), 
        $this->compile('class %s { [@experimental(stages= ["beta", "RC"])] var $fixture; }')->getField('fixture')->getAnnotations()
      );
    }
  }
?>
