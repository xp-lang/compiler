<?php namespace xp\compiler\ast;

/**
 * Represents an instance call
 *
 * ```php
 * $closure.();
 * $operation.(1, 2);
 * ```
 *
 * @see   php://call_user_func
 */
class InstanceCallNode extends Node {
  public $target= null;
  public $arguments= array();
  public $nav= false;
  
  /**
   * Creates a new InstanceCallNode object
   *
   * @param   xp.compiler.ast.Node target
   * @param   xp.compiler.ast.Node[] arguments
   * @param   bool nav
   */
  public function __construct($target= null, $arguments= null, $nav= false) {
    $this->target= $target;
    $this->arguments= $arguments;
    $this->nav= $nav;
  }
}
