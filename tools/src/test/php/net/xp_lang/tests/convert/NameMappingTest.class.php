<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_lang.convert.SourceConverter'
  );

  /**
   * TestCase
   *
   * @see      xp://cmd.convert.SourceConverter
   */
  class NameMappingTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new SourceConverter();
      $this->fixture->nameMap['Object']= 'lang.Object';
    }
    
    /**
     * Test "self"
     *
     */
    #[@test]
    public function selfKeyword() {
      $this->assertEquals('self', $this->fixture->mapName('self'));
    }

    /**
     * Test "parent"
     *
     */
    #[@test]
    public function parentKeyword() {
      $this->assertEquals('parent', $this->fixture->mapName('parent'));
    }

    /**
     * Test "xp"
     *
     */
    #[@test]
    public function xpClass() {
      $this->assertEquals('xp', $this->fixture->mapName('xp'));
    }

    /**
     * Test "lang.Object"
     *
     */
    #[@test]
    public function fullyQualified() {
      $this->assertEquals('lang.Object', $this->fixture->mapName('lang.Object'));
    }

    /**
     * Test "Object"
     *
     */
    #[@test]
    public function unQualifiedMapped() {
      $this->assertEquals('lang.Object', $this->fixture->mapName('Object'));
    }

    /**
     * Test "Object"
     *
     */
    #[@test]
    public function unQualifiedImport() {
      $this->assertEquals('Object', $this->fixture->mapName('Object', NULL, array(
        'lang.Object' => TRUE
      )));
    }

    /**
     * Test "Object"
     *
     */
    #[@test]
    public function unQualifiedMappedInLangPackage() {
      $this->assertEquals('Object', $this->fixture->mapName('Object', 'lang'));
    }

    /**
     * Test "Object"
     *
     */
    #[@test]
    public function unQualifiedMappedInUtilPackage() {
      $this->assertEquals('lang.Object', $this->fixture->mapName('Object', 'util'));
    }

    /**
     * Test:
     * <code>
     *   $package= 'lang';
     *   uses('lang.Object');
     *   
     *   class T extends Object {
     *   }
     * </code>
     */
    #[@test]
    public function unQualifiedImportedInLangPackage() {
      $this->assertEquals('Object', $this->fixture->mapName('Object', 'lang', array(
        'lang.Object' => TRUE
      )));
    }

    /**
     * Test "string"
     *
     */
    #[@test]
    public function stringPrimitive() {
      $this->assertEquals('string', $this->fixture->mapName('string'));
    }

    /**
     * Test "int"
     *
     */
    #[@test]
    public function intPrimitive() {
      $this->assertEquals('int', $this->fixture->mapName('int'));
    }

    /**
     * Test "double"
     *
     */
    #[@test]
    public function doublePrimitive() {
      $this->assertEquals('double', $this->fixture->mapName('double'));
    }

    /**
     * Test "bool"
     *
     */
    #[@test]
    public function boolPrimitive() {
      $this->assertEquals('bool', $this->fixture->mapName('bool'));
    }

    /**
     * Test "void"
     *
     */
    #[@test]
    public function voidType() {
      $this->assertEquals('void', $this->fixture->mapName('void'));
    }

    /**
     * Test "var"
     *
     */
    #[@test]
    public function variableType() {
      $this->assertEquals('var', $this->fixture->mapName('var'));
    }

    /**
     * Test "var[]"
     *
     */
    #[@test]
    public function arrayOfVariableType() {
      $this->assertEquals('var[]', $this->fixture->mapName('var[]'));
    }

    /**
     * Test "self[]"
     *
     */
    #[@test]
    public function arrayOfSelf() {
      $this->assertEquals('self[]', $this->fixture->mapName('self[]'));
    }

    /**
     * Test "lang.Object[]"
     *
     */
    #[@test]
    public function arrayOfQualified() {
      $this->assertEquals('lang.Object[]', $this->fixture->mapName('lang.Object[]'));
    }

    /**
     * Test "Object[]" being fully qualified
     *
     */
    #[@test]
    public function arrayOfUnQualifiedImport() {
      $this->assertEquals('Object[]', $this->fixture->mapName('Object[]', NULL, array(
        'lang.Object' => TRUE
      )));
    }

    /**
     * Test "lang.Object?"
     *
     */
    #[@test]
    public function unenforcedQualified() {
      $this->assertEquals('lang.Object?', $this->fixture->mapName('lang.Object?'));
    }

    /**
     * Test "lang.Object?"
     *
     */
    #[@test]
    public function unenforcedUnQualifiedImport() {
      $this->assertEquals('Object?', $this->fixture->mapName('Object?', NULL, array(
        'lang.Object' => TRUE
      )));
    }

    /**
     * Test "lang.Object?"
     *
     */
    #[@test]
    public function unenforcedUnQualifiedMappedInLangPackage() {
      $this->assertEquals('Object?', $this->fixture->mapName('Object?', 'lang'));
    }

    /**
     * Test "lang.Object?"
     *
     */
    #[@test]
    public function unenforcedUnQualifiedMappedInUtilPackage() {
      $this->assertEquals('lang.Object?', $this->fixture->mapName('Object?', 'util'));
    }

    /**
     * Tests the reference operator is removed
     *
     */
    #[@test]
    public function referencesRemovedFromPrimitives() {
      $this->assertEquals('string', $this->fixture->mapName('&string'));
    }

    /**
     * Tests the reference operator is removed
     *
     */
    #[@test]
    public function referencesRemoved() {
      $this->assertEquals('lang.Object', $this->fixture->mapName('&lang.Object'));
    }
  }
?>
