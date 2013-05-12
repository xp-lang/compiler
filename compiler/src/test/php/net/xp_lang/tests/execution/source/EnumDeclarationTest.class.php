<?php namespace net\xp_lang\tests\execution\source;

use lang\Primitive;
use lang\Enum;

/**
 * Tests class declarations
 *
 */
class EnumDeclarationTest extends ExecutionTest {

  /**
   * Test declaring an enum
   *
   */
  #[@test]
  public function coinEnum() {
    $class= self::define('enum', 'Coin', null, '{
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
    $this->assertEquals('SourceCoin', $class->getName());
    $this->assertTrue($class->isEnum());
    
    // Test values
    foreach (array(
      array('penny', 1, 'copper'),
      array('nickel', 2, 'nickel'),
      array('dime', 10, 'silver'),
      array('quarter', 25, 'silver')
    ) as $values) {
      $coin= Enum::valueOf($class, $values[0]);
      $this->assertEquals($values[0], $coin->name(), $values[0]);
      $this->assertEquals($values[1], $coin->value(), $values[0]);
      $this->assertEquals($values[2], $coin->color(), $values[0]);
    }
  }

  /**
   * Test declaring an enum
   *
   */
  #[@test]
  public function operationEnum() {
    $class= self::define('abstract enum', 'Operation', null, '{
      plus {
        public int evaluate(int $x, int $y) { return $x + $y; }
      },
      minus {
        public int evaluate(int $x, int $y) { return $x - $y; }
      };
      
      public abstract int evaluate(int $x, int $y);
    }');
    $this->assertEquals('SourceOperation', $class->getName());
    $this->assertTrue($class->isEnum());

    $plus= Enum::valueOf($class, 'plus');
    $this->assertEquals(2, $plus->evaluate(1, 1));

    $minus= Enum::valueOf($class, 'minus');
    $this->assertEquals(0, $minus->evaluate(1, 1));
  }

  /**
   * Test declaring an enum
   *
   */
  #[@test]
  public function partialOperationEnum() {
    $class= self::define('abstract enum', 'PartialOperation', null, '{
      plus {
        public int evaluate(int $x, int $y) { return $x + $y; }
      };
      
      public abstract int evaluate(int $x, int $y);
    }');
    $this->assertEquals('SourcePartialOperation', $class->getName());
    $this->assertTrue($class->isEnum());

    $plus= Enum::valueOf($class, 'plus');
    $this->assertEquals(2, $plus->evaluate(1, 1));
  }

  /**
   * Test declaring an enum
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function brokenOperationEnum() {
    self::define('enum', 'BrokenOperation', null, '{
      plus {
        public int evaluate(int $x, int $y) { return $x + $y; }
      };

      public abstract int evaluate(int $x, int $y);
    }');
  }
}
