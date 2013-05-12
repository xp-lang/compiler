<?php namespace net\xp_lang\tests\execution\source;

use lang\Primitive;
use lang\Enum;

/**
 * Tests enum declarations
 *
 * @see   xp://net.xp_lang.tests.execution.source.WeekDayEnumDeclarationTest
 * @see   xp://net.xp_lang.tests.execution.source.CoinEnumDeclarationTest
 * @see   xp://net.xp_lang.tests.execution.source.OperationEnumDeclarationTest
 */
class EnumDeclarationTest extends ExecutionTest {

  #[@test]
  public function abstract_enum_with_one_member() {
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

  #[@test, @expect('lang.FormatException')]
  public function only_abstract_enums_can_contain_members_with_bodies() {
    self::define('enum', 'BrokenOperation', null, '{
      plus {
        public int evaluate(int $x, int $y) { return $x + $y; }
      };

      public abstract int evaluate(int $x, int $y);
    }');
  }
}
