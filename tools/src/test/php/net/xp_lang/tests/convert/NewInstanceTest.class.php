<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests newinstance() is rewritten
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class NewInstanceTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function newRunnable() {
      $this->assertConversion(
        '$r= new lang.Runnable() { public void run() { /* ... */ }};',
        '$r= newinstance("lang.Runnable", array(), "{ public function run() { /* ... */ }}");',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function newRunnableWithArguments() {
      $this->assertConversion(
        '$r= new lang.Runnable($a, $b) { public void run() { /* ... */ }};',
        '$r= newinstance("lang.Runnable", array($a, $b), "{ public function run() { /* ... */ }}");',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function newRunnableWithClassConstant() {
      $this->assertConversion(
        '$r= new self($a, $b) { public void run() { /* ... */ }};',
        '$r= newinstance(__CLASS__, array($a, $b), "{ public function run() { /* ... */ }}");',
        SourceConverter::ST_FUNC_BODY
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function newRunnableWithQuotesInsideAnonymousInstanceSource() {
      $this->assertConversion(
        '$r= new self($a, $b) { public void run() { echo "Hello"; }};',
        '$r= newinstance(__CLASS__, array($a, $b), "{ public function run() { echo \"Hello\"; }}");',
        SourceConverter::ST_FUNC_BODY
      );
    }
  }
?>
