<?php namespace xp\compiler\ast;

/**
 * Operator overloading
 */
class OperatorNode extends RoutineNode {
  public $symbol      = null;
  public $returns     = null;
  public $extension   = false;

  /**
   * Returns this routine's name
   *
   * @return  string
   */
  public function getName() {
    return 'operator'.$this->symbol;
  }
}
