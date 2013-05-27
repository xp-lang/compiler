<?php namespace xp\compiler\ast;

/**
 * The "new" operator
 */
class InstanceCreationNode extends Node {
  public $type = null;
  public $parameters = null;
  public $body = null;
  
  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'new '.$this->type->name;
  }
}