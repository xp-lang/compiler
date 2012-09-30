<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests with
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class StaticInitializerConversionTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function syntaxRewritten() {
      $this->assertConversion(
        'public class Driver { static { /* ... */ } }',
        'class Driver { static function __static() { /* ... */ } }',
        SourceConverter::ST_NAMESPACE
      );
    }
  }
?>
