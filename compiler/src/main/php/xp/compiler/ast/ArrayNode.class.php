<?php namespace xp\compiler\ast;

/**
 * Represents an array literal
 * 
 * Examples:
 * ```php
 * $a= [1, 2, 3];
 * $a= new string[] { "Hello", "Hallo", "Gr�ezi" };
 * ```
 *
 * @test    xp://net.xp_lang.tests.syntax.xp.ArrayTest
 */
class ArrayNode extends Node implements Resolveable {
  public $type;
  public $values;

  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    $resolved= array();
    foreach ($this->values as $i => $value) {
      if (!$value instanceof Resolveable) {
        throw new IllegalStateException('Value at offset '.$i.' is not resolveable: '.xp::stringOf($value));
      }
      $resolved[]= $value->resolve();
    }
    return $resolved;
  }
}
