<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests lambdas
 */
class LambdaTest extends ExecutionTest {
  
  #[@test]
  public function apply() {
    $this->assertEquals(array(2, 4, 6), $this->run(
      'return apply([1, 2, 3], $a -> $a * 2);',
      array('import static net.xp_lang.tests.execution.source.Functions::apply;')
    ));
  }

  #[@test]
  public function filter() {
    $this->assertEquals(array(2, 4, 6, 8, 10), $this->run(
      'return filter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $a -> !($a & 1));',
      array('import static net.xp_lang.tests.execution.source.Functions::filter;')
    ));
  }

  #[@test]
  public function apply_capturing_local_variable() {
    $this->assertEquals(array(3, 6, 9), $this->run(
      '$mul= 3; return apply([1, 2, 3], $a -> $a * $mul);',
      array('import static net.xp_lang.tests.execution.source.Functions::apply;')
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
}
