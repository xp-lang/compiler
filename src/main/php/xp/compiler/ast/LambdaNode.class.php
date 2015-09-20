<?php namespace xp\compiler\ast;

/**
 * A lambda
 */
class LambdaNode extends Node {
  public $parameters;
  public $statements;
  public $uses;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node[] parameters
   * @param   xp.compiler.ast.Node[] statements
   * @param   xp.compiler.ast.Node[] uses
   */
  public function __construct(array $parameters, array $statements, $uses= null) {
    $this->parameters= $parameters;
    $this->statements= $statements;
    $this->uses= $uses;
  }
}
