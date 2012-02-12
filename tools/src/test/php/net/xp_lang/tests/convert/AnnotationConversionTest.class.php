<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests annotations
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class AnnotationConversionTest extends AbstractConversionTest {

    /**
     * Test simple "test" annotations
     *
     */
    #[@test]
    public function methodAnnotation() {
      $this->assertConversion(
        "[@test]\npublic void test() { /* ... */ }",
        "#[@test]\npublic function test() { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test simple "test" annotations
     *
     */
    #[@test]
    public function classAnnotation() {
      $this->assertConversion(
        "[@test]\npublic class Vector { /* ... */ }",
        "#[@test]\nclass Vector { /* ... */ }",
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test multiline annotations
     *
     */
    #[@test]
    public function multiLineAnnotation() {
      $this->assertConversion(
        "[@interceptors(classes= [\n".
        "  'security.PermissionCheck',\n".
        "  'security.RolesCheck',\n".
        "])]\n".
        "public void test() { /* ... */ }",
        "#[@interceptors(classes= array(\n".
        "#  'security.PermissionCheck',\n".
        "#  'security.RolesCheck',\n".
        "#))]\n".
        "public function test() { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }
  }
?>
