<?php namespace xp\compiler\ast;

/**
 * Represents a constant value
 *
 */
abstract class ConstantValueNode extends Node implements Resolveable {
  public $value= null;

  /**
   * Creates a new constant value node with a given value
   *
   * @param   string value
   */
  public function __construct($value= null) {
    $this->value= $value;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return $this->value;
  }
}