<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests lambdas
 */
class LambdaTest extends ExecutionTest {
  
  #[@test]
  public function apply() {
    $this->assertEquals([2, 4, 6], $this->run(
      'return apply([1, 2, 3], $a -> $a * 2);',
      ['import static net.xp_lang.tests.execution.source.Functions::apply;']
    ));
  }

  #[@test]
  public function filter() {
    $this->assertEquals([2, 4, 6, 8, 10], $this->run(
      'return filter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $a -> !($a & 1));',
      ['import static net.xp_lang.tests.execution.source.Functions::filter;']
    ));
  }

  #[@test]
  public function apply_capturing_local_variable() {
    $this->assertEquals([3, 6, 9], $this->run(
      '$mul= 3; return apply([1, 2, 3], $a -> $a * $mul);',
      ['import static net.xp_lang.tests.execution.source.Functions::apply;']
    ));
  }

  #[@test]
  public function closure_local_variables_not_captured() {
    $this->assertEquals([3, 6, 9], $this->run(
      'return apply([1, 2, 3], $a -> { $mul= 3; return $a * $mul; });',
      ['import static net.xp_lang.tests.execution.source.Functions::apply;']
    ));
  }

  #[@test]
  public function execution() {
    $this->assertEquals(3, $this->run(
      'return ($a -> $a + 1)(2);'
    ));
  }

  #[@test]
  public function execution_via_variable() {
    $this->assertEquals(3, $this->run(
      '$plusone= $a -> $a + 1; return $plusone(2);'
    ));
  }

  #[@test]
  public function inside_property_getter() {
    $class= self::define('class', 'LambdaInsidePropertyGetter', null, '{
      public var inc { get { return $a -> ++$a; } }
      public int test(int $param) { return ($this.inc)($param); } 
    }');
    $this->assertEquals(2, $class->newInstance()->test(1));
  }

  #[@test]
  public function inside_property_setter() {
    $class= self::define('class', 'LambdaInsidePropertySetter', null, '{
      private var $func;
      public var inc { set { $this.func= $a -> $a + $value; } }
      public int test(int $param) { $this.inc= $param; return ($this.func)($param); } 
    }');
    $this->assertEquals(2, $class->newInstance()->test(1));
  }

  #[@test]
  public function inside_field_initializer() {
    $class= self::define('class', 'LambdaInsideFieldInitializer', null, '{
      public var $inc= $a -> $a + 1;
      public int test(int $param) { return ($this.inc)($param); } 
    }');
    $this->assertEquals(2, $class->newInstance()->test(1));
  }
}
