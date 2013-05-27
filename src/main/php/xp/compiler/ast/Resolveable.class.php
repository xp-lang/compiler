<?php namespace xp\compiler\ast;

/**
 * Indicates a node is resolveable at compile-time
 *
 */
interface Resolveable {
 
  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve(); 
}
