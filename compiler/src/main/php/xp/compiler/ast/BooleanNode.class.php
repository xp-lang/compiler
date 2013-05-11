<?php namespace xp\compiler\ast;

/**
 * Represents a boolean literal
 *
 */
class BooleanNode extends ConstantValueNode {

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'xp.bool:'.\xp::stringOf($this->value);
  }

  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    return (bool)$this->value;
  }
}