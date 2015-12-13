<?php namespace net\xp_lang\tests\execution\source;

use lang\IllegalAccessException;

/**
 * Tests properties
 */
class PropertiesTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Sets up this test. Add uninitialized variables check
   */
  #[@beforeClass]
  public static function useUninitializedVariablesCheck() {
    self::check(new \xp\compiler\checks\UninitializedVariables(), true);
  }

  /**
   * Sets up this test. Defines StringBuffer fixture
   */
  #[@beforeClass]
  public static function defineFixture() {
    self::$fixture= self::define('class', 'StringBufferForPropertiesTest', null, '{
      protected string $buffer;

      public __construct(string $initial) {
        $this.buffer= $initial;
      }

      public int length {
        get { return strlen($this.buffer); }
        set { throw new lang.IllegalAccessException("Cannot set string length"); }
      }

      public string[] chars {
        get { return str_split($this.buffer); }
        set { $this.buffer= implode("", $value); }
      }

      public string this[int $offset] {
        get {
          return $offset >= 0 && $offset < $this.length ? $this.buffer[$offset] : null;
        }
        set {
          if (null === $offset) {
            $this.buffer ~= $value;
          } else {
            $this.buffer= substr($this.buffer, 0, $offset) ~ $value ~ substr($this.buffer, $offset+ 1);
          }
        }
        unset {
          throw new lang.IllegalAccessException("Cannot remove string offsets");
        }
        isset {
          return $offset >= 0 && $offset < $this.length;
        }
      }

      public string toString() {
        return $this.buffer;
      }
    }', [
      'import native core.strlen;', 
      'import native standard.str_split;',
      'import native standard.substr;',
      'import native standard.implode;',
    ]);
  }
  
  /**
   * Test reading the length property
   *
   */
  #[@test]
  public function readLength() {
    $str= self::$fixture->newInstance('Hello');
    $this->assertEquals(5, $str->length);
  }

  /**
   * Test reading the chars property
   *
   */
  #[@test]
  public function readChars() {
    $str= self::$fixture->newInstance('Hello');
    $this->assertEquals(['H', 'e', 'l', 'l', 'o'], $str->chars);
  }

  /**
   * Test writing the length property
   *
   */
  #[@test, @expect(IllegalAccessException::class)]
  public function writeLength() {
    $str= self::$fixture->newInstance('Hello');
    $str->length= 5;
  }

  /**
   * Test writing the length property
   *
   */
  #[@test, @expect(IllegalAccessException::class)]
  public function addLength() {
    $str= self::$fixture->newInstance('Hello');
    $str->length++;
  }

  /**
   * Test writing the chars property
   *
   */
  #[@test]
  public function writeChars() {
    $str= self::$fixture->newInstance('Hello');
    $str->chars= ['A', 'B', 'C'];
    $this->assertEquals('ABC', $str->toString());
  }

  /**
   * Test reading offsets
   *
   */
  #[@test]
  public function offsetGet() {
    $str= self::$fixture->newInstance('Hello');
    $this->assertEquals('H', $str[0], 0);
    $this->assertEquals('o', $str[4], 4);
  }

  /**
   * Test adding via []
   *
   */
  #[@test]
  public function offsetAdd() {
    $str= self::$fixture->newInstance('Hello');
    $str[]= '!';
    $this->assertEquals('Hello!', $str->toString());
  }

  /**
   * Test writing to offsets
   *
   */
  #[@test]
  public function offsetSet() {
    $str= self::$fixture->newInstance('Hello');
    $str[1]= 'a';
    $this->assertEquals('Hallo', $str->toString());
  }

  /**
   * Test testing offsets
   *
   */
  #[@test]
  public function offsetExists() {
    $str= self::$fixture->newInstance('Hello');
    $this->assertTrue(isset($str[0]));
    $this->assertTrue(isset($str[4]));
    $this->assertFalse(isset($str[-1]));
    $this->assertFalse(isset($str[5]));
  }

  /**
   * Test removing offsets
   *
   */
  #[@test, @expect(IllegalAccessException::class)]
  public function offsetUnset() {
    $str= self::$fixture->newInstance('Hello');
    unset($str[0]);
  }

  /**
   * Test reading non-existant offsets
   *
   */
  #[@test]
  public function getNonExistantOffset() {
    $str= self::$fixture->newInstance('Hello');
    $this->assertEquals(null, $str[-1], -1);
    $this->assertEquals(null, $str[5], 5);
  }
}
