<?php namespace xp\compiler\types;

/**
 * Represents the routine scope
 *
 * @see     xp://xp.compiler.Scope
 */
class MethodScope extends Scope {
  public $routine= null;

  /**
   * Constructor
   *
   * @param   xp.compiler.ast.RoutineNode $routine
   */
  public function __construct($routine= null) {
    $this->routine= $routine;
    parent::__construct();
  }
}