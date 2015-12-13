<?php namespace xp\compiler\ast;

/**
 * Represents a static method call
 *
 * ```php
 * self::connect();
 * ```
 */
class StaticMethodCallNode extends Node {
  public $type= null;
  public $name= '';
  public $arguments= [];
  
  /**
   * Creates a new InvocationNode object
   *
   * @param   xp.compiler.types.TypeName type
   * @param   string name
   * @param   xp.compiler.ast.Node[] arguments
   */
  public function __construct($type= null, $name= '', $arguments= null) {
    $this->type= $type;
    $this->name= $name;
    $this->arguments= $arguments;
  }
}