<?php namespace xp\compiler\ast;

/**
 * The "yield from" statement
 */
class YieldFromNode extends Node {
  public $expr;

  public function __construct($expr) {
    $this->expr= $expr;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'yield from '.$this->expr->hashCode();
  }
}