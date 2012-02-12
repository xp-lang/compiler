<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests TRUE, FALSE and NULL are rewritten
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class LiteralsRewritingTest extends AbstractConversionTest {

    /**
     * Test NULL
     *
     */
    #[@test]
    public function null() {
      $this->assertConversion(
        '$a= null;',
        '$a= NULL;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test TRUE
     *
     */
    #[@test]
    public function true() {
      $this->assertConversion(
        '$a= true;',
        '$a= TRUE;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test FALSE
     *
     */
    #[@test]
    public function false() {
      $this->assertConversion(
        '$a= false;',
        '$a= FALSE;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function insideAnArray() {
      $this->assertConversion(
        '$a= [true, false, null];',
        '$a= array(TRUE, FALSE, NULL);',
        SourceConverter::ST_FUNC_BODY
      );
    }
  }
?>
