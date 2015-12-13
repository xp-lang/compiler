<?php namespace xp\compiler\ast;

/**
 * Represents an array literal
 * 
 * Examples:
 * ```php
 * $a= [1, 2, 3];
 * $a= new string[] { "Hello", "Hallo", "Grüezi" };
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
    $resolved= [];
    foreach ($this->values as $i => $value) {
      if (!$value instanceof Resolveable) {
        throw new \lang\IllegalStateException('Value at offset '.$i.' is not resolveable: '.\xp::stringOf($value));
      }
      $resolved[]= $value->resolve();
    }
    return $resolved;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    $s= '';
    foreach ($this->values as $i => $value) {
      $s.= ', '.$value->toString();
    }
    return ($this->type ? $this->type->compoundName() : 'var[]').' {'.substr($s, 2).'}';
  }
}
