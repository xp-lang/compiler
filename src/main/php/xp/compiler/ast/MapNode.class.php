<?php namespace xp\compiler\ast;

/**
 * Represents a map literal
 */
class MapNode extends Node implements Resolveable {
  public $type;
  public $elements;

  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    $resolved= [];
    foreach ($this->elements as $i => $pair) {
      if (!$pair[0] instanceof Resolveable || !$pair[1] instanceof Resolveable) {
        throw new \lang\IllegalStateException('Pair at offset '.$i.' is not resolveable: '.\xp::stringOf($pair));
      }
      $resolved[$pair[0]->resolve()]= $pair[1]->resolve();
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
    foreach ($this->elements as $pair) {
      $s.= ', '.$pair[0]->toString().' : '.$pair[1]->toString();
    }
    return ($this->type ? $this->type->compoundName() : '[:var]').' {'.substr($s, 2).'}';
  }
}