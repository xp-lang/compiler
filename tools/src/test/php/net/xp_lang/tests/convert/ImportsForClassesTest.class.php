<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests import statements are created
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class ImportsForClassesTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function usesConvertedToImport() {
      $this->assertConversion("\n\n".
        "import util.Date;\n\npublic class DateUtil { }",
        "uses('util.Date');\n\nclass DateUtil { }",
        SourceConverter::ST_NAMESPACE,
        'de.thekid.util.dates.DateUtil'
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function noSamePackageImport() {
      $this->assertConversion("\n\n".
        "public class DateUtil { }",
        "uses('util.Date');\n\nclass DateUtil { }",
        SourceConverter::ST_NAMESPACE,
        'util.DateUtil'
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function noLangImport() {
      $this->assertConversion("\n\n".
        "public class DateUtil { }",
        "uses('lang.Runtime');\n\nclass DateUtil { }",
        SourceConverter::ST_NAMESPACE,
        'util.DateUtil'
      );
    }
  }
?>
