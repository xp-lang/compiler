<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests parameter types
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class ParameterTypesAggregationTest extends AbstractConversionTest {

    /**
     * Test no parameter types
     *
     */
    #[@test]
    public function noParameterTypes() {
      $this->assertConversion(
        "public void test(var \$a) { /* ... */ }",
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test string parameter type
     *
     */
    #[@test]
    public function stringParameterType() {
      $this->assertConversion(
        "/**\n".
        " * @param   string a\n".
        " */\n".
        "public void test(string \$a) { /* ... */ }",
        "/**\n".
        " * @param   string a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test lang.Generic parameter type
     *
     */
    #[@test]
    public function genericParameterType() {
      $this->assertConversion(
        "/**\n".
        " * @param   lang.Generic a\n".
        " */\n".
        "public void test(lang.Generic \$a) { /* ... */ }",
        "/**\n".
        " * @param   lang.Generic a\n".
        " */\n".
        "public function test(Generic \$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test lang.Generic parameter type
     *
     */
    #[@test]
    public function genericParameterTypeWithoutRestriction() {
      $this->assertConversion(
        "/**\n".
        " * @param   lang.Generic a\n".
        " */\n".
        "public void test(lang.Generic? \$a) { /* ... */ }",
        "/**\n".
        " * @param   lang.Generic a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test lang.Generic parameter type
     *
     */
    #[@test]
    public function referenceOperatorRemoved() {
      $this->assertConversion(
        "/**\n".
        " * @param   &lang.Generic a\n".
        " */\n".
        "public void test(lang.Generic \$a) { /* ... */ }",
        "/**\n".
        " * @param   &lang.Generic a\n".
        " */\n".
        "public function test(Generic \$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test "mixed" parameter type becomes "var"
     *
     */
    #[@test]
    public function mixedBecomesVar() {
      $this->assertConversion(
        "/**\n".
        " * @param   mixed a\n".
        " */\n".
        "public void test(var \$a) { /* ... */ }",
        "/**\n".
        " * @param   mixed a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test "mixed" parameter type becomes "var"
     *
     */
    #[@test]
    public function mixedArrayBecomesVar() {
      $this->assertConversion(
        "/**\n".
        " * @param   mixed[] a\n".
        " */\n".
        "public void test(var[] \$a) { /* ... */ }",
        "/**\n".
        " * @param   mixed[] a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test "float" parameter type becomes "double"
     *
     */
    #[@test]
    public function floatBecomesDouble() {
      $this->assertConversion(
        "/**\n".
        " * @param   float a\n".
        " */\n".
        "public void test(double \$a) { /* ... */ }",
        "/**\n".
        " * @param   float a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test "resource" parameter type becomes "Int"
     *
     */
    #[@test]
    public function resourceBecomesInt() {
      $this->assertConversion(
        "/**\n".
        " * @param   resource a\n".
        " */\n".
        "public void test(int \$a) { /* ... */ }",
        "/**\n".
        " * @param   resource a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test "array<key, val>" parameter type becomes "[:val]"
     *
     */
    #[@test]
    public function genericArrayBecomesMap() {
      $this->assertConversion(
        "/**\n".
        " * @param   array<int, string> a\n".
        " */\n".
        "public void test([:string]? \$a) { /* ... */ }",
        "/**\n".
        " * @param   array<int, string> a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test "array" parameter type becomes "var[]"
     *
     */
    #[@test]
    public function arrayBecomesVarArray() {
      $this->assertConversion(
        "/**\n".
        " * @param   array a\n".
        " */\n".
        "public void test(var[] \$a) { /* ... */ }",
        "/**\n".
        " * @param   array a\n".
        " */\n".
        "public function test(\$a) { /* ... */ }",
        SourceConverter::ST_DECL
      );
    }
  }
?>
