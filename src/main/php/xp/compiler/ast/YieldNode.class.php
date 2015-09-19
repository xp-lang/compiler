<?php namespace xp\compiler\ast;

/**
 * The "yield" statement
 */
class YieldNode extends Node {
  public $value= null;
  public $key= null;

  public function __construct($value= null, $key= null) {
    $this->value= $value;
    $this->key= $key;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'yield '.($this->key ? $this->key->hashCode().' => ' : '').($this->value ? $this->value->hashCode() : '');
  }
}