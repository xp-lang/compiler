<?php namespace net\xp_lang\tests\execution\source;

use lang\Enum;
use lang\Primitive;

/**
 * Tests enum declaration:
 *
 * ```php
 * enum WeekDay {
 *   MON, TUE, WED, THU, FRI, SAT, SUN;
 *
 *   public bool isWeekend() {
 *     return $this.ordinal > self::$FRI.ordinal;
 *   }
 * ```
 */
class WeekDayEnumDeclarationTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Creates fixture
   */
  #[@beforeClass]
  public static function defineWeekdayEnumClass() {
    self::$fixture= self::define('enum', 'WeekDay', null, '{
      MON, TUE, WED, THU, FRI, SAT, SUN;
      
      public bool isWeekend() {
        return $this.ordinal > self::$FRI.ordinal;
      }
    }');
  }

  #[@test]
  public function class_name() {
    $this->assertEquals('SourceWeekDay', self::$fixture->getName());
  }

  #[@test]
  public function is_an_enum() {
    $this->assertTrue(self::$fixture->isEnum());
  }

  #[@test]
  public function weekend_method_declaration() {
    with (self::$fixture->getMethod('isWeekend'), function($method) {
      $this->assertEquals('isWeekend', $method->getName());
      $this->assertEquals(MODIFIER_PUBLIC, $method->getModifiers());
      $this->assertEquals(Primitive::$BOOL, $method->getReturnType());
      $this->assertEquals(0, $method->numParameters());
    });
  }

  /**
   * Returns members and ordinals for use in the following two tests
   *
   * @return  var[]
   */
  public function members() {
    return [
      ['MON', 0],
      ['TUE', 1],
      ['WED', 2],
      ['THU', 3],
      ['FRI', 4],
      ['SAT', 5],
      ['SUN', 6]
    ];
  }

  #[@test, @values('members')]
  public function member_name($name, $ordinal) {
    $this->assertEquals($name, Enum::valueOf(self::$fixture, $name)->name());
  }

  #[@test, @values('members')]
  public function member_ordinal($name, $ordinal) {
    $this->assertEquals($ordinal, Enum::valueOf(self::$fixture, $name)->ordinal());
  }

  #[@test]
  public function sunday_is_weekend() {
    $this->assertTrue(Enum::valueOf(self::$fixture, 'SUN')->isWeekend());
  }

  #[@test]
  public function monday_is_not_a_weekend() {
    $this->assertFalse(Enum::valueOf(self::$fixture, 'MON')->isWeekend());
  }
}
