<?php namespace xp\compiler\ast;

/**
 * Represents a return statement.
 *
 * ```php
 * return;
 * return $a;
 * ```
 */
class ReturnNode extends Node {
  public $expression= null;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node expression
   */
  public function __construct(Node $expression= null) {
    $this->expression= $expression;
  }
}
