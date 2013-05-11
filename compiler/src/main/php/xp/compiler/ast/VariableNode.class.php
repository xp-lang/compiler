<?php namespace xp\compiler\ast;

/**
 * Represents a variable
 *
 */
class VariableNode extends Node {
  public $name= '';
  
  /**
   * Constructor
   *
   * @param   string name
   */
  public function __construct($name= '') {
    $this->name= $name;
  }
  
  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '$'.$this->name;
  }

  /**
   * Returns whether another object equals this.
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }
}
