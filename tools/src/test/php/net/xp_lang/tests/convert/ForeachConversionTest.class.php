<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests foreach
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class ForeachConversionTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function asValue() {
      $this->assertConversion(
        'foreach ($v in $a) { /* ... */ }',
        'foreach ($a as $v) { /* ... */ }',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function arrayAsValue() {
      $this->assertConversion(
        'foreach ($v in [1, 2, 3]) { /* ... */ }',
        'foreach (array(1, 2, 3) as $v) { /* ... */ }',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function asKeyValue() {
      $this->assertConversion(
        'foreach ($k, $v in $m) { /* ... */ }',
        'foreach ($m as $k => $v) { /* ... */ }',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function mapAsKeyValue() {
      $this->assertConversion(
        'foreach ($k, $v in ["foo" : "bar"]) { /* ... */ }',
        'foreach (array("foo" => "bar") as $k => $v) { /* ... */ }',
        SourceConverter::ST_FUNC_BODY
      );
    }
  }
?>
