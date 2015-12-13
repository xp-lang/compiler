<?php namespace xp\compiler\ast;

/**
 * Represents a list of statements
 */
class StatementsNode extends Node {
  public $list= [];
  
  /**
   * Constructor.
   *
   * @param   xp.compiler.ast.Node[] initial
   */
  public function __construct(array $initial= []) {
    $this->list= $initial;
  }
}
