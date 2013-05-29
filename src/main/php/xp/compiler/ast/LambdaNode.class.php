<?php namespace xp\compiler\ast;

/**
 * A lambda
 */
class LambdaNode extends Node {
  public $parameters;
  public $statements;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node[] parameters
   * @param   xp.compiler.ast.Node[] statements
   */
  public function __construct(array $parameters, array $statements) {
    $this->parameters= $parameters;
    $this->statements= $statements;
  }
}
