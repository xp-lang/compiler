<?php namespace xp\compiler\ast;

/**
 * Represents an enum member
 *
 * @see   xp://xp.compiler.EnumNode
 */
class EnumMemberNode extends TypeMemberNode {
  public $value = null;
  public $body  = null;
  
  /**
   * Returns this members's hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '$'.$this->getName();
  }
}