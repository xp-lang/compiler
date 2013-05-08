<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\MapTypeOf;
use xp\compiler\types\TypeReflection;
use lang\XPClass;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.types.MapTypeOf
 */
class MapTypeOfTest extends \unittest\TestCase {
  protected $fixture= NULL;
  
  /**
   * Set up test case - creates fixture
   *
   */
  public function setUp() {
    $this->fixture= new MapTypeOf(new TypeReflection(XPClass::forName('lang.XPClass')));
  }

  /**
   * Test name()
   *
   */
  #[@test]
  public function name() {
    $this->assertEquals('[:lang.XPClass]', $this->fixture->name());
  }

  /**
   * Test literal()
   *
   */
  #[@test]
  public function literal() {
    $this->assertEquals('array', $this->fixture->literal());
  }

  /**
   * Test isSubclassOf()
   *
   */
  #[@test]
  public function isSubclassOfTypeMap() {
    $this->assertTrue($this->fixture->isSubclassOf(new MapTypeOf(
      new TypeReflection(XPClass::forName('lang.Type'))
    )));
  }

  /**
   * Test isSubclassOf()
   *
   */
  #[@test]
  public function isNotSubclassOfType() {
    $this->assertFalse($this->fixture->isSubclassOf(new TypeReflection(XPClass::forName('lang.Type'))));
  }

  /**
   * Test getExtensions()
   *
   */
  #[@test]
  public function mapTypeDoesNotHaveExtensions() {
    $this->assertEquals(array(), $this->fixture->getExtensions());
  }
}
