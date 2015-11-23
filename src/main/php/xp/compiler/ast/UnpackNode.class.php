<?php namespace xp\compiler\ast;

/**
 * Represents an argument to be unpacked
 */
class UnpackNode extends Node {
  public $expression;
  
  /**
   * Constructor
   *
   * @param  xp.compiler.ast.Node $expression
   */
  public function __construct($expression) {
    $this->expression= $expression;
  }

  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '...'.$this->expression->hashCode();
  }
}
