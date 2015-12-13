<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests varargs
 *
 * @see   http://java.sun.com/j2se/1.5.0/docs/guide/language/varargs.html
 */
class VarArgsTest extends ExecutionTest {

  /**
   * Test 
   *
   */
  #[@test]
  public function intArray() {
    $class= self::define('class', $this->name, null, '{
      public int[] $values;
      
      public __construct(int... $values) {
        $this.values= $values;
      }
    }');
    $this->assertEquals([1, 2, 3], $class->newInstance(1, 2, 3)->values);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function stringFormat() {
    $class= self::define('class', $this->name, null, '{
      public static string format(string $f, var... $args) {
        return vsprintf($f, $args);
      }
    }', ['import native standard.vsprintf;']);

    $this->assertEquals(
      'Hello World #1',
      $class->getMethod('format')->invoke(null, ['Hello %s #%d', 'World', 1])
    );
  }
}
