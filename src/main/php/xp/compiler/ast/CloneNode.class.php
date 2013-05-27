<?php namespace xp\compiler\ast;

/**
 * Represents the clone statement
 *
 * ```php
 * $clone= clone $expression;
 * ```
 */
class CloneNode extends Node {
  public $expression;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node expression
   */
  public function __construct(Node $expression) {
    $this->expression= $expression;
  }
}