<?php namespace net\xp_lang\tests\execution\source;

class GeneratorTest extends ExecutionTest {

  #[@test]
  public function yield_an_integer_value() {
    $this->assertEquals(
      [1],
      iterator_to_array($this->run('yield 1;'))
    );
  }

  #[@test]
  public function yield_key_and_value() {
    $this->assertEquals(
      ['number' => 1],
      iterator_to_array($this->run('yield "number" : 1;'))
    );
  }
}