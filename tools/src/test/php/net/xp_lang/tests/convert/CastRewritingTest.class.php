<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests cast rewriting
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class CastRewritingTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function stringCast() {
      $this->assertConversion(
        'return $a as string;',
        'return (string)$a;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function intCast() {
      $this->assertConversion(
        'return $a as int;',
        'return (int)$a;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function doubleCast() {
      $this->assertConversion(
        'return $a as double;',
        'return (double)$a;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function boolCast() {
      $this->assertConversion(
        'return $a as bool;',
        'return (bool)$a;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayCast() {
      $this->assertConversion(
        'return $a as var[];',
        'return (array)$a;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayCastOfMember() {
      $this->assertConversion(
        'return $this.elements as var[];',
        'return (array)$this->elements;',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayCastOfMethod() {
      $this->assertConversion(
        'return $this.getMethods() as var[];',
        'return (array)$this->getMethods();',
        SourceConverter::ST_FUNC_BODY
      );
    }
  }
?>
