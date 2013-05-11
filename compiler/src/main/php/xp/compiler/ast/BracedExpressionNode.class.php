<?php namespace xp\compiler\ast;

/**
 * Represents a braced expression:
 *
 * ```php
 * ( 3 + 2 ) * 5
 * ```
 * 
 * Braces are used for precedence.
 */
class BracedExpressionNode extends Node {
  public $expression= null;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node expression
   */
  public function __construct(Node $expression) {
    $this->expression= $expression;
  }
  
  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '('.$this->expression->hashCode().')';
  }
}
