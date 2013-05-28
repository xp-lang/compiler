<?php namespace xp\compiler\ast;

/**
 * Native import statement
 */
class NativeImportNode extends Node {
  public $name= '';

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'xp.import.native:'.$this->name;
  }
}
