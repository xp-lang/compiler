<?php namespace xp\compiler\ast;

/**
 * Represents a method call
 *
 * ```php
 * $this.connect();
 * ```
 */
class MethodCallNode extends Node {
  public $target= null;
  public $name= '';
  public $arguments= array();
  public $nav= false;
  
  /**
   * Creates a new method cal instance
   *
   * @param   xp.compiler.ast.Node target
   * @param   string name
   * @param   xp.compiler.ast.Node[] arguments
   * @param   bool nav
   */
  public function __construct($target= null, $name= '', $arguments= null, $nav= false) {
    $this->target= $target;
    $this->name= $name;
    $this->arguments= $arguments;
    $this->nav= $nav;
  }
}
