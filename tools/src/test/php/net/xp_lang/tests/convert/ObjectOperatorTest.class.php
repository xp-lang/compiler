<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests the object operator is rewritten
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class ObjectOperatorTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function memberAccess() {
      $this->assertConversion(
        '$this.buffer= "";',
        '$this->buffer= "";',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function memberCall() {
      $this->assertConversion(
        '$this.finalize();',
        '$this->finalize();',
        SourceConverter::ST_FUNC_BODY
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function chain() {
      $this->assertConversion(
        '$this.getMethod("toString").invoke($this);',
        '$this->getMethod("toString")->invoke($this);',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function notInsideString() {
      $this->assertConversion(
        '$operator= $this.symbols["->"];',
        '$operator= $this->symbols["->"];',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function notInsideComments() {
      $this->assertConversion(
        '$a= "Hello"; /* -> */',
        '$a= "Hello"; /* -> */',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function notInsideSingleLineComments() {
      $this->assertConversion(
        '$a= "Hello"; // ->',
        '$a= "Hello"; // ->',
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
        '$a= [$this.getMethod("toString")];',
        '$a= array($this->getMethod("toString"));',
        SourceConverter::ST_FUNC_BODY
      );
    }
  }
?>
