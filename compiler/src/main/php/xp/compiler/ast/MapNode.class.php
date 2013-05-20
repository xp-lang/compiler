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
    $resolved= array();
    foreach ($this->elements as $i => $pair) {
      if (!$pair[0] instanceof Resolveable || !$pair[1] instanceof Resolveable) {
        throw new \lang\IllegalStateException('Pair at offset '.$i.' is not resolveable: '.\xp::stringOf($pair));
      }
      $resolved[$pair[0]->resolve()]= $pair[1]->resolve();
    }
    return $resolved;
  }
}