<?php namespace xp\compiler\ast;

/**
 * Represents a string literal
 */
class StringNode extends ConstantValueNode {

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'xp.string:'.$this->value;
  }

  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    return (string)$this->value;
  }
}
