<?php namespace xp\compiler\ast;

/**
 * Silence operator
 *
 * Example
 * ~~~~~~~
 * ```php
 * @$a[0];    // Get array element, suppress warning if !isset
 * ```
 *
 * Note
 * ~~~~
 * This is only available in PHP syntax!
 */
class SilenceOperatorNode extends Node {
  public $expression = null;
  
  /**
   * Creates a new dynamic variable reference
   *
   * @param  xp.compiler.ast.Node expression
   */
  public function __construct(Node $expression) {
    $this->expression= $expression;
  }
}
