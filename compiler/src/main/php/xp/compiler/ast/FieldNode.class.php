<?php namespace xp\compiler\ast;

/**
 * Represents a field
 *
 */
class FieldNode extends TypeMemberNode {
  public $type           = null;
  public $initialization = null;

  /**
   * Returns this members's hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '$'.$this->getName();
  }
}