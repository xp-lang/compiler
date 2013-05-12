<?php namespace net\xp_lang\tests\execution\source;

use lang\Primitive;
use lang\Enum;

/**
 * Tests enum declarations
 */
class EnumDeclarationTest extends ExecutionTest {

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
