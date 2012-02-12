<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests create() is rewritten
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class CreateConversionTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function newStringVector() {
      $this->assertConversion(
        '$r= new util.collections.Vector<lang.types.String>();',
        '$r= create("new util.collections.Vector<lang.types.String>()");',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function newStringVectorWithArgs() {
      $this->assertConversion(
        '$r= new util.collections.Vector<string>("Hello", "World");',
        '$r= create("new util.collections.Vector<string>", array("Hello", "World"));',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function createAsDereferencerUntouched() {
      $this->name('Object', 'lang.Object');
      $this->assertConversion(
        '$r= create(new lang.Object());',
        '$r= create(new Object());',
        SourceConverter::ST_FUNC_BODY
      );
    }
  }
?>
