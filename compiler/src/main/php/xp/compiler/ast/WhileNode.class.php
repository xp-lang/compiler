<?php namespace xp\compiler\ast;

/**
 * Represents a while-statement
 *
 * <code>
 *   while (...) {
 *     ...
 *   }
 * </code>
 */
class WhileNode extends Node {
  public $statements= null;
  public $expression= null;

  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node expression
   * @param   xp.compiler.ast.Node[] statements
   */
  public function __construct(Node $expression= null, $statements= array()) {
    $this->expression= $expression;
    $this->statements= $statements;
  }
}
