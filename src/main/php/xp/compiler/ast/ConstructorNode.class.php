<?php namespace xp\compiler\ast;

/**
 * Represents a constructor
 */
class ConstructorNode extends RoutineNode {
  public
    $modifiers  = 0,
    $parameters = [];
  
  /**
   * Returns this members's name
   *
   * @return  string
   */
  public function getName() {
    return '__construct';
  }
}