<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests field rewriting
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class FieldRewritingTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function publicMember() {
      $this->assertConversion(
        'public var $test;',
        'public $test;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function listOfPublicMembers() {
      $this->assertConversion(
        'public var $test;'."\n  ".'public var $unit;',
        'public $test, $unit;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function protectedMember() {
      $this->assertConversion(
        'protected var $test;',
        'protected $test;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function listOfProtectedMembers() {
      $this->assertConversion(
        'protected var $test;'."\n  ".'protected var $unit;',
        'protected $test, $unit;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function privateMember() {
      $this->assertConversion(
        'private var $test;',
        'private $test;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function listOfPrivateMembers() {
      $this->assertConversion(
        'private var $test;'."\n  ".'private var $unit;',
        'private $test, $unit;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function staticMember() {
      $this->assertConversion(
        'static var $test;',
        'static $test;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function listOfStaticMembers() {
      $this->assertConversion(
        'static var $test;'."\n  ".'static var $unit;',
        'static $test, $unit;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function publicStaticMember() {
      $this->assertConversion(
        'public static var $test;',
        'public static $test;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function listOfPublicStaticMembers() {
      $this->assertConversion(
        'public static var $test;'."\n  ".'public static var $unit;',
        'public static $test, $unit;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function memberWithInitialization() {
      $this->assertConversion(
        'public var $reg= true;',
        'public $reg= TRUE;',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function memberWithArrayOfNullInitialization() {
      $this->assertConversion(
        'public var $regs= [null];',
        'public $regs= array(NULL);',
        SourceConverter::ST_DECL
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function memberWithArrayInitialization() {
      $this->assertConversion(
        'public var $dim= [1, 2];',
        'public $dim= array(1, 2);',
        SourceConverter::ST_DECL
      );
    }
  }
?>
