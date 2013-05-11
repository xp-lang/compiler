<?php namespace xp\compiler\ast;

/**
 * Represents an array access operator
 *
 * Examples:
 * ```php
 * $first= $list[0];
 * $element= $map[$key];
 * ```
 */
class ArrayAccessNode extends Node {
  public $target= null;
  public $offset= null;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node target
   * @param   xp.compiler.ast.Node offset
   */
  public function __construct($target= null, Node $offset= null) {
    $this->target= $target;
    $this->offset= $offset;
  }
  
  /**
   * Returns a hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '['.($this->offset ? $this->offset->hashCode() : '').']';
  }
}
