<?php namespace net\xp_lang\tests\execution\source;

use util\collections\HashTable;
use lang\IllegalArgumentException;

/**
 * Tests type restrictions in parameters
 */
class TypeRestrictionTest extends ExecutionTest {

  /**
   * Compile statements and return type
   *
   * @param   string type
   * @param   bool ok
   * @return  lang.Generic
   */
  protected function signature($signature) {
    $type= self::define(
      'class', 
      ucfirst($this->name).'·'.($this->counter++), 
      null,
      '{ public bool accept('.$signature.') { return true; }}',
      ['import util.collections.*;']
    );
    return $type->newInstance();
  }

  /**
   * Assert executing a block will yield a type mismatch
   *
   * @param  function(): void $block
   * @throws unittest.AssertionFailedError
   */
  protected function assertTypeMismatch($block) {
    try {
      $block();
      $this->fail('No exception raised', null, ['lang.IllegalArgumentException', 'TypeError']);
    } catch (IllegalArgumentException $expected) {
      // OK, PHP 5.X
    } catch (\TypeError $expected) {
      // OK, PHP 7.X
    }
  }

  /**
   * Test passing a string to a string type hint
   *
   */
  #[@test]
  public function primitiveVsPrimitive() {
    $this->assertTrue($this->signature('string $arg', true)->accept('string'));
  }

  /**
   * Test passing a string to an Object type hint
   *
   */
  #[@test]
  public function primitiveVsObject() {
    $this->assertTypeMismatch(function() {
      $this->signature('Object $arg')->accept('string');
    });
  }

  /**
   * Test passing null to an Object type hint
   *
   */
  #[@test]
  public function nullVsObject() {
    $this->assertTypeMismatch(function() {
      $this->signature('Object $arg')->accept(null);
    });
  }

  /**
   * Test passing null to an Object type hint with null default
   *
   */
  #[@test]
  public function nullVsObjectWithNullDefault() {
    $this->assertTrue($this->signature('Object $arg= null')->accept(null));
  }

  /**
   * Test passing an object to a primitive type hint
   *
   */
  #[@test, @expect(IllegalArgumentException::class)]
  public function objectVsPrimitive() {
    $this->signature('string $arg')->accept($this);
  }

  /**
   * Test passing null to a primitive type hint
   *
   */
  #[@test]
  public function nulllVsPrimitive() {
    $this->assertTrue($this->signature('string $arg')->accept(null));
  }

  /**
   * Test passing a string to a string[] type hint
   *
   */
  #[@test]
  public function primitiveVsArray() {
    $this->assertTypeMismatch(function() {
      $this->signature('string[] $arg')->accept('string');
    });
  }

  /**
   * Test passing an object to a string[] type hint
   *
   */
  #[@test]
  public function objectVsArray() {
    $this->assertTypeMismatch(function() {
      $this->signature('string[] $arg')->accept($this);
    });
  }

  /**
   * Test generic version of util.collections.HashTable to a generic type hint
   *
   */
  #[@test]
  public function genericVsGenericHashTable() {
    $this->assertTrue($this->signature('HashTable<string, string> $arg')->accept(create('new util.collections.HashTable<string, string>')));
  }

  /**
   * Test generic version of util.collections.HashTable to a generic type hint
   *
   */
  #[@test]
  public function genericHashTableVsGenericMap() {
    $this->assertTrue($this->signature('Map<string, string> $arg')->accept(create('new util.collections.HashTable<string, string>')));
  }

  /**
   * Test non-generic vs. generic version of util.collections.HashTable
   *
   */
  #[@test]
  public function nonGenericVsGenericHashTable() {
    $this->assertTypeMismatch(function() {
      $this->signature('HashTable<string, string> $arg')->accept(new HashTable());
    });
  }

  /**
   * Test generic version of util.collections.HashTable to a generic type hint
   *
   */
  #[@test, @ignore('Broken for the moment')]
  public function genericOfGenericsVsGenericOfGenericsHashTable() {
    $this->assertTrue($this->signature('HashTable<string, Vector<int>> $arg')->accept(('new util.collections.HashTable<string, util.collections.Vector<int>>')));
  }

  /**
   * Test passing an object to a var type hint
   *
   */
  #[@test]
  public function objectVsVar() {
    $this->assertTrue($this->signature('var $arg')->accept($this));
  }

  /**
   * Test passing a string to a var type hint
   *
   */
  #[@test]
  public function primitiveVsVar() {
    $this->assertTrue($this->signature('var $arg')->accept('string'));
  }

  /**
   * Test passing null to a var type hint
   *
   */
  #[@test]
  public function nullVsVar() {
    $this->assertTrue($this->signature('var $arg')->accept(null));
  }

  /**
   * Test passing an array to a var type hint
   *
   */
  #[@test]
  public function arrayVsVar() {
    $this->assertTrue($this->signature('var $arg')->accept([1, 2, 3]));
  }
}
