<?php namespace xp\compiler\ast;

/**
 * The "with" statement
 *
 */
class WithNode extends Node {
  public $assignments;
  public $statements;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node[] assignment
   * @param   xp.compiler.ast.Node[] statements
   */
  public function __construct(array $assignments, array $statements) {
    $this->assignments= $assignments;
    $this->statements= $statements;
  }
}