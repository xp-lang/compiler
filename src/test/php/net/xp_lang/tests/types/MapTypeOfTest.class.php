<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\MapTypeOf;
use xp\compiler\types\TypeReflection;
use lang\XPClass;

class MapTypeOfTest extends \unittest\TestCase {
  private $fixture;
  
  /**
   * Set up test case - creates fixture
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new MapTypeOf(new TypeReflection(XPClass::forName('lang.XPClass')));
  }

  #[@test]
  public function name() {
    $this->assertEquals('[:lang.XPClass]', $this->fixture->name());
  }

  #[@test]
  public function literal() {
    $this->assertEquals('array', $this->fixture->literal());
  }

  #[@test]
  public function isSubclassOfTypeMap() {
    $this->assertTrue($this->fixture->isSubclassOf(new MapTypeOf(
      new TypeReflection(XPClass::forName('lang.Type'))
    )));
  }

  #[@test]
  public function isNotSubclassOfType() {
    $this->assertFalse($this->fixture->isSubclassOf(new TypeReflection(XPClass::forName('lang.Type'))));
  }

  #[@test]
  public function mapTypeDoesNotHaveExtensions() {
    $this->assertEquals(array(), $this->fixture->getExtensions());
  }
}
