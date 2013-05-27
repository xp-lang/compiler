<?php namespace xp\compiler\ast;

/**
 * An import statement
 */
class ImportNode extends Node {
  public $name= '';

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'xp.import:'.$this->name;
  }
}
