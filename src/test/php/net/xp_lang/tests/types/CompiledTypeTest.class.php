<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\CompiledType;
use xp\compiler\types\TypeReflection;
use xp\compiler\types\Types;
use xp\compiler\types\TypeName;
use xp\compiler\types\Field;
use xp\compiler\types\Method;
use xp\compiler\types\Parameter;
use xp\compiler\types\Property;
use xp\compiler\types\Constant;
use xp\compiler\types\Operator;
use lang\XPClass;

class CompiledTypeTest extends \unittest\TestCase {
  private $fixture= null;
  private static $objectType;
  
  static function __static() {
    self::$objectType= new TypeReflection(XPClass::forName('lang.Object'));
  }
  
  /**
   * Set up test case - creates fixture
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new CompiledType(ucfirst($this->name));
  }

  /**
   * Returns fixture with a given parent class
   *
   * @param   xp.compiler.types.Types parent
   * @return  xp.compiler.types.Types fixture
   */
  private function fixtureWithParent($parent) {
    $this->fixture->parent= $parent;
    return $this->fixture;
  }

  #[@test]
  public function method() {
    $m= new Method('hello');
    $m->returns= new TypeName('string');
    $this->fixture->addMethod($m);
    $this->assertEquals($m, $this->fixture->getMethod($m->name));
  }

  #[@test]
  public function field() {
    $f= new Field('name');
    $f->type= new TypeName('string');
    $this->fixture->addField($f);
    $this->assertEquals($f, $this->fixture->getField($f->name));
  }

  #[@test]
  public function constant() {
    $f= new Constant('name');
    $f->type= new TypeName('string');
    $this->fixture->addConstant($f);
    $this->assertEquals($f, $this->fixture->getConstant($f->name));
  }

  #[@test]
  public function property() {
    $f= new Property('name');
    $f->type= new TypeName('string');
    $this->fixture->addProperty($f);
    $this->assertEquals($f, $this->fixture->getProperty($f->name));
  }

  #[@test]
  public function operator() {
    $o= new Operator('+');
    $o->returns= new TypeName('string');
    $this->fixture->addOperator($o);
    $this->assertEquals($o, $this->fixture->getOperator($o->symbol));
  }

  #[@test]
  public function isNotSubclassOfSelf() {
    $this->fixture->parent= new TypeReflection(XPClass::forName('unittest.TestCase'));
    $this->assertFalse($this->fixture->isSubclassOf($this->fixture));
  }

  #[@test]
  public function isSubclassOfParent() {
    $this->fixture->parent= new TypeReflection(XPClass::forName('unittest.TestCase'));
    $this->assertTrue($this->fixture->isSubclassOf($this->fixture->parent));
  }


  #[@test]
  public function isSubclassOfParentsParent() {
    $this->fixture->parent= new TypeReflection(XPClass::forName('unittest.TestCase'));
    $this->assertTrue($this->fixture->isSubclassOf(new TypeReflection(XPClass::forName('lang.Object'))));
  }
  
  #[@test]
  public function hasParentMethod() {
    $this->assertTrue($this->fixtureWithParent(self::$objectType)->hasMethod('getClassName'));
  }

  #[@test]
  public function getParentMethod() {
    $m= $this->fixtureWithParent(self::$objectType)->getMethod('getClassName');
    $this->assertInstanceOf(Method::class, $m);
    $this->assertEquals($this->fixture->parent(), $m->holder);
  }

  #[@test]
  public function hasOverwrittenMethod() {
    $m= new Method('getClassName');
    $m->returns= new TypeName('string');
    $this->fixtureWithParent(self::$objectType)->addMethod($m);
    $this->assertTrue($this->fixture->hasMethod('getClassName'));
  }

  #[@test]
  public function getOverwrittenMethod() {
    $m= new Method('getClassName');
    $m->returns= new TypeName('string');
    $this->fixtureWithParent(self::$objectType)->addMethod($m);
    $m= $this->fixture->getMethod('getClassName');
    $this->assertInstanceOf(Method::class, $m);
    $this->assertEquals($this->fixture, $m->holder);
  }

  #[@test]
  public function noParentHasMethod() {
    $this->assertFalse($this->fixtureWithParent(null)->hasMethod('getClassName'));
  }

  #[@test]
  public function noParentParentMethod() {
    $this->assertNull($this->fixtureWithParent(null)->getMethod('getClassName'));
  }

  #[@test]
  public function emptyTypeHasNoExtensions() {
    $this->assertEquals([], $this->fixture->getExtensions());
  }

  #[@test]
  public function getExtensions() {
    $m= new Method('sorted');
    $m->modifiers= MODIFIER_PUBLIC | MODIFIER_STATIC;
    $m->returns= new TypeName('lang.types.ArrayList');
    $m->parameters= [new Parameter('self', new TypeName('lang.types.ArrayList'))];
    $this->fixture->addMethod($m, new TypeName('lang.types.ArrayList'));
    $extensions= $this->fixture->getExtensions();

    $this->assertEquals(1, sizeof($extensions));
    $this->assertEquals('lang.types.ArrayList', key($extensions));
    $this->assertEquals('sorted', $extensions['lang.types.ArrayList'][0]->name());
  }
}
