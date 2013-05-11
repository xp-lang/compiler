<?php namespace xp\compiler\ast;

/**
 * Represents a static member access
 *
 * ```php
 * self::$member;
 * ```
 */
class StaticMemberAccessNode extends Node {
  public $type= null;
  public $name= '';
  
  /**
   * Constructor
   *
   * @param   xp.compiler.types.TypeName type
   * @param   string name
   */
  public function __construct($type= null, $name= '') {
    $this->type= $type;
    $this->name= $name;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return $this->type->compoundName().'::$'.$this->name;
  }
  
  /**
   * Returns whether another object equals this.
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return 
      $cmp instanceof self && 
      $this->type->equals($cmp->type) &&
      $this->name === $cmp->name
    ;
  }
}
