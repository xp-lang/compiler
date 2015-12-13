<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\TypeInstance;
use xp\compiler\types\TypeReflection;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use xp\compiler\types\Parameter;
use lang\XPClass;

class TypeInstanceTest extends \unittest\TestCase {

  #[@test]
  public function runnable_interface_instance_tostring_exists() {
    $this->assertTrue((new TypeInstance(new TypeReflection(XPClass::forName('lang.Runnable'))))->hasMethod('toString'));
  }

  #[@test]
  public function runnable_interface_instance_tostring_return() {
    $m= (new TypeInstance(new TypeReflection(XPClass::forName('lang.Runnable'))))->getMethod('toString');
    $this->assertEquals(new TypeName('string'), $m->returns);
  }

  #[@test]
  public function runnable_interface_instance_equals_parameters() {
    $m= (new TypeInstance(new TypeReflection(XPClass::forName('lang.Runnable'))))->getMethod('equals');
    $this->assertEquals(
      [new Parameter('cmp', new TypeName(XPClass::forName('lang.Object')->getMethod('equals')->getParameter(0)->getTypeName()))],
      $m->parameters
    );
  }

  #[@test]
  public function object_class_has_no_extension_methods() {
    $this->assertEquals(
      [], 
      (new TypeInstance(new TypeReflection(XPClass::forName('lang.Object'))))->getExtensions()
    );
  }

  #[@test]
  public function extension_methods() {
    $extensions= (new TypeInstance(new TypeReflection(XPClass::forName('net.xp_lang.tests.types.ArraySortingExtensions'))))->getExtensions();

    $this->assertEquals(1, sizeof($extensions));
    $this->assertEquals('lang.types.ArrayList', key($extensions));
    $this->assertEquals('sorted', $extensions['lang.types.ArrayList'][0]->name());
  }
}
