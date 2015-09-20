<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\GenericType;
use xp\compiler\types\TypeReflection;
use xp\compiler\types\Types;
use xp\compiler\types\Parameter;
use xp\compiler\types\TypeName;
use lang\XPClass;

class GenericTypeTest extends \unittest\TestCase {

  /**
   * Returns new util.collections.HashTable fixture
   *
   * @return  xp.compiler.types.GenericType
   */
  private function newGenericHashTableType() {
    return new GenericType(
      new TypeReflection(XPClass::forName('util.collections.HashTable')), 
      array(new TypeName('string'), new TypeName('lang.Object'))
    );
  }    

  /**
   * Returns new util.collections.Vector fixture
   *
   * @return  xp.compiler.types.GenericType
   */
  private function newGenericVectorType() {
    return new GenericType(
      new TypeReflection(XPClass::forName('util.collections.Vector')), 
      array(new TypeName('string'))
    );
  }    

  #[@test]
  public function rewriteSimpleType() {
    $this->assertEquals(
      new TypeName('string'),
      $this->newGenericHashTableType()->rewrite(new TypeName('K'))
    );
  }

  #[@test]
  public function rewriteArrayType() {
    $this->assertEquals(
      new TypeName('string[]'),
      $this->newGenericHashTableType()->rewrite(new TypeName('K[]'))
    );
  }

  #[@test]
  public function rewriteMapType() {
    $this->assertEquals(
      new TypeName('[:string]'),
      $this->newGenericHashTableType()->rewrite(new TypeName('[:K]'))
    );
  }

  #[@test]
  public function rewriteGenericListTypeWithPlaceholder() {
    $this->assertEquals(
      new TypeName('List', array(new TypeName('string'))),
      $this->newGenericHashTableType()->rewrite(new TypeName('List', array(new TypeName('K'))))
    );
  }

  #[@test]
  public function rewriteGenericListTypeWithoutPlaceholder() {
    $this->assertEquals(
      new TypeName('List', array(new TypeName('int'))),
      $this->newGenericHashTableType()->rewrite(new TypeName('List', array(new TypeName('int'))))
    );
  }

  #[@test]
  public function rewriteGenericMapTypeBothPlaceholders() {
    $this->assertEquals(
      new TypeName('Map', array(new TypeName('string'), new TypeName('lang.Object'))),
      $this->newGenericHashTableType()->rewrite(new TypeName('Map', array(new TypeName('K'), new TypeName('V'))))
    );
  }

  #[@test]
  public function rewriteGenericMapTypeOnePlaceholder() {
    $this->assertEquals(
      new TypeName('Map', array(new TypeName('string'), new TypeName('int'))),
      $this->newGenericHashTableType()->rewrite(new TypeName('Map', array(new TypeName('K'), new TypeName('int'))))
    );
  }

  #[@test]
  public function rewriteInt() {
    $this->assertEquals(
      new TypeName('int'),
      $this->newGenericHashTableType()->rewrite(new TypeName('int'))
    );
  }

  #[@test]
  public function rewriteTypeContainingComponentName() {
    $this->assertEquals(
      new TypeName('Key'),
      $this->newGenericHashTableType()->rewrite(new TypeName('Key'))
    );
  }
  
  #[@test]
  public function hashTableIndexerType() {
    $this->assertEquals(new TypeName('lang.Object'), $this->newGenericHashTableType()->getIndexer()->type);
  }

  #[@test]
  public function hashTableIndexerParameters() {
    $this->assertEquals(new TypeName('string'), $this->newGenericHashTableType()->getIndexer()->parameter);
  }

  #[@test]
  public function hashTableGetMethodType() {
    $this->assertEquals(new TypeName('lang.Object'), $this->newGenericHashTableType()->getMethod('get')->returns);
  }

  #[@test]
  public function hashTableGetMethodParameters() {
    $this->assertEquals(
      array(new Parameter('key', new TypeName('string'))),
      $this->newGenericHashTableType()->getMethod('get')->parameters
    );
  }

  #[@test]
  public function hashTableKeysMethodReturns() {
    $this->assertEquals(new TypeName('string[]'), $this->newGenericHashTableType()->getMethod('keys')->returns);
  }

  #[@test]
  public function vectorIndexerType() {
    $this->assertEquals(new TypeName('string'), $this->newGenericVectorType()->getIndexer()->type);
  }

  #[@test]
  public function vectorIndexerParameters() {
    $this->assertEquals(new TypeName('int'), $this->newGenericVectorType()->getIndexer()->parameter);
  }

  #[@test]
  public function vectorGetMethodType() {
    $this->assertEquals(new TypeName('string'), $this->newGenericVectorType()->getMethod('get')->returns);
  }

  #[@test]
  public function vectorGetMethodParameters() {
    $this->assertEquals(
      array(new Parameter('index', new TypeName('int'))),
      $this->newGenericVectorType()->getMethod('get')->parameters
    );
  }
}
