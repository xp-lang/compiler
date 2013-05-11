<?php namespace xp\compiler\ast;

/**
 * Represents a member access
 *
 * ```php
 * $this.member;
 * ```
 */
class MemberAccessNode extends Node {
  public $target= null;
  public $name= '';
  public $nav= false;

  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node target
   * @param   string name
   * @param   bool nav
   */
  public function __construct($target= null, $name= '', $nav= false) {
    $this->target= $target;
    $this->name= $name;
    $this->nav= $nav;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '$'.$this->target->hashCode().'->'.$this->name;
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
      $this->target->equals($cmp->target) &&
      $this->name === $cmp->name
    ;
  }
}
