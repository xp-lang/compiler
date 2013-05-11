<?php namespace xp\compiler\ast;

/**
 * The "try (...) { block }" statement - Automatic Resource Management
 *
 */
class ArmNode extends Node {
  public $initializations;
  public $variables;
  public $statements;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node[] declarations
   * @param   xp.compiler.ast.Node[] variables
   * @param   xp.compiler.ast.Node[] statements
   */
  public function __construct(array $initializations, array $variables, array $statements) {
    $this->initializations= $initializations;
    $this->variables= $variables;
    $this->statements= $statements;
  }
}
