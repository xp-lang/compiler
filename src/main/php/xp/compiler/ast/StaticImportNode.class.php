<?php namespace xp\compiler\ast;

/**
 * Import static statement
 */
class StaticImportNode extends Node {
  public $name= '';

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'xp.import.static:'.$this->name;
  }
}
