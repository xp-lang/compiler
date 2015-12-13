<?php namespace xp\compiler\ast;

/**
 * Represents a do-while-statement
 *
 * ```php
 * do {
 *   ...
 * } while (...);
 * ```
 */
class DoNode extends Node {
  public $statements= null;
  public $expression= null;

  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node expression
   * @param   xp.compiler.ast.Node[] statements
   */
  public function __construct(Node $expression= null, $statements= []) {
    $this->expression= $expression;
    $this->statements= $statements;
  }
}
