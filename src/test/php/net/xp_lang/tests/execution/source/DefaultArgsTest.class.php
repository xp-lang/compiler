<?php namespace net\xp_lang\tests\execution\source;

use lang\Object;
use util\Date;
use lang\XPClass;

/**
 * Tests default arguments
 *
 */
class DefaultArgsTest extends ExecutionTest {
  
  /**
   * Sets up test case and define class to be used in tests
   *
   */
  public function fixtureClass() {
    return self::define('class', $this->name, null, '{
      public int[] $values;
      
      public __construct(int $a, int $b= 2) {
        $this.values= [$a, $b];
      }
    }');
  }
  
  /**
   * Test 
   *
   */
  #[@test]
  public function omitted() {
    $this->assertEquals([1, 2], $this->fixtureClass()->newInstance(1)->values);
  }

  /**
   * Test 
   *
   */
  #[@test]
  public function passed() {
    $this->assertEquals([1, 2], $this->fixtureClass()->newInstance(1, 2)->values);
  }

  /**
   * Test 
   *
   */
  #[@test]
  public function overridden() {
    $this->assertEquals([2, 3], $this->fixtureClass()->newInstance(2, 3)->values);
  }

  /**
   * Test 
   *
   */
  #[@test]
  public function complex() {
    $class= self::define('class', $this->name, null, '{
      public static Generic newInstance(XPClass $class= Object::class) {
        return $class.newInstance();
      }
    }');
    with ($i= $class->getMethod('newInstance')); {
      $this->assertInstanceOf(Object::class, $i->invoke(null, []));
      $this->assertInstanceOf(Date::class, $i->invoke(null, [XPClass::forName('util.Date')]));
    }
  }
}
