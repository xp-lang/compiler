<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\TypeName;
use xp\compiler\types\Types;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.types.TypeName
 */
class TypeNameTest extends \unittest\TestCase {

  #[@test]
  public function varIsVariable() {
    $this->assertTrue(TypeName::$VAR->isVariable());
  }

  #[@test]
  public function voidIsNotVariable() {
    $this->assertFalse(TypeName::$VOID->isVariable());
  }

  #[@test]
  public function objectIsNotVariable() {
    $this->assertFalse((new TypeName('lang.Object'))->isVariable());
  }

  #[@test]
  public function varIsNotVoid() {
    $this->assertFalse(TypeName::$VAR->isVoid());
  }

  #[@test]
  public function voidIsVoid() {
    $this->assertTrue(TypeName::$VOID->isVoid());
  }

  #[@test]
  public function objectIsNotVoid() {
    $this->assertFalse((new TypeName('lang.Object'))->isVoid());
  }

  #[@test]
  public function intIsPrimitive() {
    $this->assertTrue((new TypeName('int'))->isPrimitive());
  }

  #[@test]
  public function objectIsNotPrimitive() {
    $this->assertFalse((new TypeName('lang.Object'))->isPrimitive());
  }

  #[@test]
  public function intArrayIsArray() {
    $this->assertTrue((new TypeName('int[]'))->isArray());
  }

  #[@test]
  public function intIsNotArray() {
    $this->assertFalse((new TypeName('int'))->isArray());
  }

  #[@test]
  public function intMapIsMap() {
    $this->assertTrue((new TypeName('[:int]'))->isMap());
  }

  #[@test]
  public function intIsNotMap() {
    $this->assertFalse((new TypeName('int'))->isMap());
  }

  #[@test]
  public function intArrayIsNotMap() {
    $this->assertFalse((new TypeName('int[]'))->isMap());
  }

  #[@test]
  public function genericListIsGeneric() {
    $this->assertTrue((new TypeName('List', array(new TypeName('T'))))->isGeneric());
  }

  #[@test]
  public function arrayIsNotGeneric() {
    $this->assertFalse((new TypeName('T[]'))->isGeneric());
  }

  #[@test]
  public function intPrimitiveCompoundName() {
    $this->assertEquals('int', (new TypeName('int'))->compoundName());
  }

  #[@test]
  public function stringArrayCompoundName() {
    $this->assertEquals('string[]', (new TypeName('string[]'))->compoundName());
  }

  #[@test]
  public function objectClassCompoundName() {
    $this->assertEquals('lang.Object', (new TypeName('lang.Object'))->compoundName());
  }

  #[@test]
  public function genericListCompoundName() {
    $this->assertEquals('List<T>', (new TypeName('List', array(new TypeName('T'))))->compoundName());
  }

  #[@test]
  public function arrayComponentType() {
    $this->assertEquals(new TypeName('string'), (new TypeName('string[]'))->arrayComponentType());
  }

  #[@test]
  public function arrayComponentTypeOfNonArray() {
    $this->assertEquals(null, (new TypeName('string'))->arrayComponentType());
  }

  #[@test]
  public function mapComponentType() {
    $this->assertEquals(
      new TypeName('int'), 
      (new TypeName('[:int]'))->mapComponentType()
    );
  }

  #[@test]
  public function mapComponentTypeOfNonMap() {
    $this->assertEquals(null, (new TypeName('string'))->mapComponentType());
  }

  #[@test]
  public function tIsPlaceHolderInListOfT() {
    $decl= new TypeName('List', array(new TypeName('T')));
    $this->assertTrue($decl->isPlaceholder(new TypeName('T')));
  }

  #[@test]
  public function kIsNotPlaceHolderInListOfT() {
    $decl= new TypeName('List', array(new TypeName('T')));
    $this->assertFalse($decl->isPlaceholder(new TypeName('K')));
  }

  #[@test]
  public function kAndVArePlaceHoldersInMapOfKV() {
    $decl= new TypeName('Map', array(new TypeName('K'), new TypeName('V')));
    $this->assertTrue($decl->isPlaceholder(new TypeName('K')), 'K');
    $this->assertTrue($decl->isPlaceholder(new TypeName('V')), 'V');
  }

  #[@test]
  public function intIsNotFunction() {
    $this->assertFalse((new TypeName('int'))->isFunction());
  }

  #[@test]
  public function isFunction() {
    $this->assertTrue((new TypeName('->string', array(new TypeName('int'))))->isFunction());
  }

  #[@test]
  public function functionReturnType() {
    $this->assertEquals(new TypeName('string'), (new TypeName('->string', array()))->functionReturnType());
  }
}