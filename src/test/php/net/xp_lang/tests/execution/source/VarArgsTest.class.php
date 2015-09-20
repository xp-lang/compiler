<?php namespace net\xp_lang\tests\execution\source;

class VarArgsTest extends ExecutionTest {

  #[@test]
  public function int_array() {
    $class= self::define('class', $this->name, null, '{
      public int[] $values;
      
      public __construct(int... $values) {
        $this.values= $values;
      }
    }');
    $this->assertEquals(array(1, 2, 3), $class->newInstance(1, 2, 3)->values);
  }

  #[@test]
  public function string_format() {
    $class= self::define('class', $this->name, null, '{
      public static string format(string $f, var... $args) {
        return vsprintf($f, $args);
      }
    }', array('import native standard.vsprintf;'));

    $this->assertEquals(
      'Hello World #1',
      $class->getMethod('format')->invoke(null, array('Hello %s #%d', 'World', 1))
    );
  }
}
