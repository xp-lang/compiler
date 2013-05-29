<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\TypeInstance;
use xp\compiler\types\TypeReflection;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use lang\XPClass;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.types.TypeInstance
 */
class TypeInstanceTest extends \unittest\TestCase {

  /**
   * Test
   *
   */
  #[@test]
  public function runnableInterfaceInstanceHasToStringMethod() {
    $this->assertTrue(create(new TypeInstance(new TypeReflection(XPClass::forName('lang.Runnable'))))->hasMethod('toString'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function runnableInterfaceInstanceToString() {
    $m= create(new TypeInstance(new TypeReflection(XPClass::forName('lang.Runnable'))))->getMethod('toString');
    $this->assertEquals(new TypeName('string'), $m->returns);
  }

  /**
   * Test getExtensions() method
   *
   */
  #[@test]
  public function objectClassHasNoExtensionMethods() {
    $this->assertEquals(
      array(), 
      create(new TypeInstance(new TypeReflection(XPClass::forName('lang.Object'))))->getExtensions()
    );
  }

  /**
   * Test getExtensions() method
   *
   */
  #[@test]
  public function extensionMethod() {
    $extensions= create(new TypeInstance(new TypeReflection(XPClass::forName('net.xp_lang.tests.types.ArraySortingExtensions'))))->getExtensions();

    $this->assertEquals(1, sizeof($extensions));
    $this->assertEquals('lang.types.ArrayList', key($extensions));
    $this->assertEquals('sorted', $extensions['lang.types.ArrayList'][0]->name());
  }
}
