<?php namespace net\xp_lang\tests\execution\source;

use unittest\actions\RuntimeVersion;

#[@action(new RuntimeVersion('>=5.5.0'))]
class GeneratorTest extends ExecutionTest {

  #[@test]
  public function yield_by_itself() {
    $this->assertEquals(
      [null],
      iterator_to_array($this->run('yield;'))
    );
  }

  #[@test]
  public function yield_an_integer_value() {
    $this->assertEquals(
      [1],
      iterator_to_array($this->run('yield 1;'))
    );
  }

  #[@test]
  public function yield_string_values() {
    $this->assertEquals(
      ['one', 'two', 'three'],
      iterator_to_array($this->run('yield "one"; yield "two"; yield "three";'))
    );
  }

  #[@test]
  public function yield_key_and_value() {
    $this->assertEquals(
      ['number' => 1],
      iterator_to_array($this->run('yield "number" : 1;'))
    );
  }

  #[@test]
  public function yield_from_generator() {
    $this->assertEquals(
      [0, 1, 2, 3],
      iterator_to_array($this->run('yield 0; yield from (() -> { yield 1; yield 2; })(); yield 3;'))
    );
  }

  #[@test]
  public function yield_from_iterator() {
    $this->assertEquals(
      [0, 1, 2, 3],
      iterator_to_array($this->run('yield 0; yield from new php.ArrayIterator([1, 2]); yield 3;'))
    );
  }

  #[@test]
  public function yield_from_an_array() {
    $this->assertEquals(
      [0, 1, 2, 3],
      iterator_to_array($this->run('yield 0; yield from [1, 2]; yield 3;'))
    );
  }
}