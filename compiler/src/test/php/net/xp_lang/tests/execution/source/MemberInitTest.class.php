<?php namespace net\xp_lang\tests\execution\source;

use lang\XPClass;

/**
 * Tests member initialization
 *
 */
class MemberInitTest extends ExecutionTest {

  /**
   * Sets up test case and adds IsAsssignale check
   *
   */
  public function setUp() {
    parent::setUp();
    $this->check(new \xp\compiler\checks\UninitializedVariables(), true);
  }

  /**
   * Creates a new instance
   *
   * @param   string src
   * @return  lang.Generic
   */
  protected function newInstance($src) {
    return $this->define('class', $this->getName(), null, $src)->newInstance();
  }

  /**
   * Test member initialized to an empty array
   *
   */
  #[@test]
  public function toEmptyArray() {
    $this->assertEquals(array(), $this->newInstance('{ public Object[] $images= []; }')->images);
  }

  /**
   * Test member initialized to an array of ints
   *
   */
  #[@test]
  public function toNonEmptyArray() {
    $this->assertEquals(array(1, 2, 3), $this->newInstance('{ public int[] $list= [1, 2, 3]; }')->list);
  }

  /**
   * Test member initialized to an array of ints
   *
   */
  #[@test]
  public function toNonEmptyMap() {
    $this->assertEquals(array('one' => 'two'), $this->newInstance('{ public [:string] $map= [ one : "two"]; }')->map);
  }

  /**
   * Test member initialized to an empty string
   *
   */
  #[@test]
  public function toNonEmptyString() {
    $this->assertEquals('Name', $this->newInstance('{ public string $name= "Name"; }')->name);
  }

  /**
   * Test member initialized to an empty string
   *
   */
  #[@test]
  public function toEmptyString() {
    $this->assertEquals('', $this->newInstance('{ public string $name= ""; }')->name);
  }

  /**
   * Test member initialized to 0
   *
   */
  #[@test]
  public function toZero() {
    $this->assertEquals(0, $this->newInstance('{ public int $id= 0; }')->id);
  }

  /**
   * Test member initialized to 1
   *
   */
  #[@test]
  public function toOne() {
    $this->assertEquals(1, $this->newInstance('{ public int $id= 1; }')->id);
  }

  /**
   * Test member initialized to -1
   *
   */
  #[@test]
  public function toNegativeOne() {
    $this->assertEquals(-1, $this->newInstance('{ public int $id= -1; }')->id);
  }

  /**
   * Test member initialized to null
   *
   */
  #[@test]
  public function toNull() {
    $this->assertNull($this->newInstance('{ public string $name= null; }')->name);
  }

  /**
   * Test member initialized to true
   *
   */
  #[@test]
  public function toTrue() {
    $this->assertTrue($this->newInstance('{ public bool $flag= true; }')->flag);
  }

  /**
   * Test member initialized to false
   *
   */
  #[@test]
  public function toFalse() {
    $this->assertFalse($this->newInstance('{ public bool $flag= false; }')->flag);
  }

  /**
   * Test member initialized to new T()
   *
   */
  #[@test]
  public function toNewInstance() {
    $this->assertInstanceOf('util.Date', $this->newInstance('{ public util.Date $now= new util.Date(); }')->now);
  }

  /**
   * Test complex example also found in demo package
   *
   */
  #[@test]
  public function complexExample() {
    $this->assertEquals(array(1, 2, 3), $this->newInstance('{ 
      public static XPClass $arrayClass= lang.types.ArrayList::class;  
      public int[] $elements= self::$arrayClass.newInstance(1, 2, 3).values;
    }')->elements);
  }

  /**
   * Test complex example also found in demo package
   *
   */
  #[@test]
  public function anonymousClasses() {
    $i= $this->newInstance('{ 
      public Object newAnonymousInstance() {
        return new Object() {
          public static XPClass $arrayClass= lang.types.ArrayList::class;

          public XPClass getMember() { return self::$arrayClass; }
        };
      }
    }');
    $this->assertEquals(
      XPClass::forName('lang.types.ArrayList'), 
      $i->newAnonymousInstance()->getMember()
    );
  }
}
