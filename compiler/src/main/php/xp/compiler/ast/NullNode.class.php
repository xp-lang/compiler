<?php namespace xp\compiler\ast;

/**
 * Represents the null literal
 */
class NullNode extends ConstantValueNode {

  /**
   * Creates a new constant value node with a given value
   *
   * @param   string value
   */
  public function __construct($value= null) {
    parent::__construct(null);
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'xp.null';
  }

  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    return null;
  }
}
