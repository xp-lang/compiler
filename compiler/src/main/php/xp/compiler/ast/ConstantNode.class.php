<?php namespace xp\compiler\ast;

/**
 * Represents a constant
 */
class ConstantNode extends Node {
  public $name= null;

  /**
   * Creates a new constant value node with a given name
   *
   * @param   string name
   */
  public function __construct($name= null) {
    $this->name= $name;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return $this->name;
  }
}
