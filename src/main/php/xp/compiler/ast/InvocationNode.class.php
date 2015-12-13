<?php namespace xp\compiler\ast;

/**
 * Represents an invocation:
 *
 * ```php
 * writeLine();   // via import static = Console::writeLine()
 * exec();        // via import native = php.standard.exec()
 * ```
 *
 * @see   xp://xp.compiler.ast.MethodCallNode
 */
class InvocationNode extends Node {
  public $name= '';
  public $arguments= [];
  
  /**
   * Creates a new InvocationNode object
   *
   * @param   string name
   * @param   xp.compiler.ast.Node[] arguments
   */
  public function __construct($name, $arguments= null) {
    $this->name= $name;
    $this->arguments= $arguments;
  }
  
  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return $this->name.'()';
  }
}
