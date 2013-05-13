<?php namespace net\xp_lang\tests\execution\source;

use lang\Enum;

/**
 * Tests enum declaration:
 *
 * ```php
 * enum Coin {
 *   penny(1), nickel(2), dime(10), quarter(25);
 * 
 *   public int value() {
 *     return $this.ordinal;
 *   }
 * 
 *   public string color() {
 *     switch ($this) {
 *       case self::$penny: return "copper";
 *       case self::$nickel: return "nickel";
 *       case self::$dime: case self::$quarter: return "silver";
 *     }
 *   }
 * }
 * ```
 */
class CoinEnumDeclarationTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Creates fixture
   */
  #[@beforeClass]
  public static function defineCoinClass() {
    self::$fixture= self::define('enum', 'Coin', null, '{
      penny(1), nickel(2), dime(10), quarter(25);

      public int value() {
        return $this.ordinal;
      }

      public string color() {
        switch ($this) {
          case self::$penny: return "copper";
          case self::$nickel: return "nickel";
          case self::$dime: case self::$quarter: return "silver";
        }
      }
    }');
  }

  #[@test]
  public function class_name() {
    $this->assertEquals('SourceCoin', self::$fixture->getName());
  }

  #[@test]
  public function is_an_enum() {
    $this->assertTrue(self::$fixture->isEnum());
  }

  /**
   * Returns members and ordinals for use in the following four tests
   *
   * @return  var[]
   */
  public function members() {
    return array(
      array('penny', 1, 'copper'),
      array('nickel', 2, 'nickel'),
      array('dime', 10, 'silver'),
      array('quarter', 25, 'silver')
    );
  }

  #[@test, @values('members')]
  public function member_name($name, $value, $color) {
    $this->assertEquals($name, Enum::valueOf(self::$fixture, $name)->name());
  }

  #[@test, @values('members')]
  public function member_ordinal($name, $value, $color) {
    $this->assertEquals($value, Enum::valueOf(self::$fixture, $name)->ordinal());
  }

  #[@test, @values('members')]
  public function member_value($name, $value, $color) {
    $this->assertEquals($value, Enum::valueOf(self::$fixture, $name)->value());
  }

  #[@test, @values('members')]
  public function member_color($name, $ordinal, $color) {
    $this->assertEquals($color, Enum::valueOf(self::$fixture, $name)->color());
  }
}
