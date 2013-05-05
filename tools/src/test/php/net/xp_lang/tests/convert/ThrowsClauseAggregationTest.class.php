<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests throws clase
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class ThrowsClauseAggregationTest extends AbstractConversionTest {

    /**
     * Test no throws clause
     *
     */
    #[@test]
    public function noThrowsClause() {
      $this->assertConversion(
        "public void test() { /* ... */ }",
        "public function test() { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test one throws
     *
     */
    #[@test]
    public function oneThrownException() {
      $this->assertConversion(
        "/**\n".
        " * @throws   lang.IllegalArgumentException in case of an error\n".
        " */\n".
        "public void test() throws lang.IllegalArgumentException { /* ... */ }",
        "/**\n".
        " * @throws   lang.IllegalArgumentException in case of an error\n".
        " */\n".
        "public function test() { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test multiple throws
     *
     */
    #[@test]
    public function multipleThrownExceptions() {
      $this->assertConversion(
        "/**\n".
        " * @throws   lang.IllegalArgumentException in case of an error\n".
        " * @throws   lang.IllegalStateException if not connected\n".
        " */\n".
        "public void test() throws lang.IllegalArgumentException, lang.IllegalStateException { /* ... */ }",
        "/**\n".
        " * @throws   lang.IllegalArgumentException in case of an error\n".
        " * @throws   lang.IllegalStateException if not connected\n".
        " */\n".
        "public function test() { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }
 
    /**
     * Test multiple throws
     *
     */
    #[@test]
    public function thrownExceptionsAreUniqued() {
      $this->assertConversion(
        "/**\n".
        " * @throws   lang.IllegalArgumentException in case of an error\n".
        " * @throws   lang.IllegalStateException if not connected\n".
        " * @throws   lang.IllegalStateException if disconnected\n".
        " */\n".
        "public void test() throws lang.IllegalArgumentException, lang.IllegalStateException { /* ... */ }",
        "/**\n".
        " * @throws   lang.IllegalArgumentException in case of an error\n".
        " * @throws   lang.IllegalStateException if not connected\n".
        " * @throws   lang.IllegalStateException if disconnected\n".
        " */\n".
        "public function test() { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }
  }
?>
