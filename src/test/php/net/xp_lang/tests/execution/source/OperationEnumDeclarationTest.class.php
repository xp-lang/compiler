<?php namespace net\xp_lang\tests\execution\source;

use lang\Enum;

/**
 * Tests enum declaration:
 *
 * ```php
 * abstract enum Operation {
 *   plus {
 *     public int evaluate(int $x, int $y) { return $x + $y; }
 *   },
 *   minus {
 *     public int evaluate(int $x, int $y) { return $x - $y; }
 *   };
 *   
 *   public abstract int evaluate(int $x, int $y);
 * }
 * ```
 */
class OperationEnumDeclarationTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Creates fixture
   */
  #[@beforeClass]
  public static function defineOperationClass() {
    self::$fixture= self::define('abstract enum', 'Operation', null, '{
      plus {
        public int evaluate(int $x, int $y) { return $x + $y; }
      },
      minus {
        public int evaluate(int $x, int $y) { return $x - $y; }
      };
      
      public abstract int evaluate(int $x, int $y);
    }');
  }

  #[@test]
  public function class_name() {
    $this->assertEquals('SourceOperation', self::$fixture->getName());
  }

  #[@test]
  public function is_an_enum() {
    $this->assertTrue(self::$fixture->isEnum());
  }

  #[@test]
  public function is_abstract() {
    $this->assertTrue(\lang\reflect\Modifiers::isAbstract(self::$fixture->getModifiers()));
  }

  /**
   * Returns members and ordinals for use in the following two tests
   *
   * @return  var[]
   */
  public function members() {
    return array(
      array('plus', 0),
      array('minus', 1)
    );
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
  public function plus() {
    $this->assertEquals(3, Enum::valueOf(self::$fixture, 'plus')->evaluate(1, 2));
  }

  #[@test]
  public function minus() {
    $this->assertEquals(-1, Enum::valueOf(self::$fixture, 'minus')->evaluate(1, 2));
  }
}
