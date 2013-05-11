<?php namespace xp\compiler\ast;

/**
 * Dynamic variable reference
 *
 * Example
 * ~~~~~~~
 * ```php
 * $this->$name;
 * $this->{$name};
 * $this->{substr($name, 0, -5)};
 * ```
 *
 * Note
 * ~~~~
 * This is only available in PHP syntax!
 *
 */
class DynamicVariableReferenceNode extends Node {
  public $target= null;
  public $expression = null;
  
  /**
   * Creates a new dynamic variable reference
   *
   * @param   xp.compiler.ast.Node target
   * @param  xp.compiler.ast.Node expression
   */
  public function __construct($target= null, Node $expression) {
    $this->target= $target;
    $this->expression= $expression;
  }
}
