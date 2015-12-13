<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\ArrayTypeOf;
use xp\compiler\types\TypeReflection;
use lang\XPClass;

class ArrayTypeOfTest extends \unittest\TestCase {
  private $fixture;
  
  /**
   * Set up test case - creates fixture
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.XPClass')));
  }

  #[@test]
  public function name() {
    $this->assertEquals('lang.XPClass[]', $this->fixture->name());
  }

  #[@test]
  public function literal() {
    $this->assertEquals('array', $this->fixture->literal());
  }

  #[@test]
  public function parent() {
    $this->assertEquals(new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Type'))), $this->fixture->parent());
  }

  #[@test]
  public function objectArrayHasNoParent() {
    $this->assertNull((new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Object'))))->parent());
  }

  #[@test]
  public function isSubclassOfObjectArray() {
    $this->assertTrue($this->fixture->isSubclassOf(new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Object')))));
  }

  #[@test]
  public function isNotSubclassOfObject() {
    $this->assertFalse($this->fixture->isSubclassOf(new TypeReflection(XPClass::forName('lang.Object'))));
  }

  #[@test]
  public function arrayTypeDoesNotHaveExtensions() {
    $this->assertEquals([], $this->fixture->getExtensions());
  }
}
