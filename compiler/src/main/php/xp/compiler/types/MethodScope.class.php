<?php namespace xp\compiler\types;

/**
 * Represents the method scope
 *
 * @see     xp://xp.compiler.Scope
 */
class MethodScope extends Scope {
  public $name= null;

  /**
   * Constructor
   *
   * @param   string name
   */
  public function __construct($name= null) {
    $this->name= $name;
    parent::__construct();
  }
}