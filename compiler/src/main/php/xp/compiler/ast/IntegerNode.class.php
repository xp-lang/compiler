<?php namespace xp\compiler\ast;

/**
 * Represents an integer literal
 *
 * @see   xp://xp.compiler.ast.NaturalNode
 */
class IntegerNode extends NaturalNode {

  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    return (int)$this->value;
  }
}