<?php namespace xp\compiler\ast;

/**
 * The "foreach" loop
 */
class ForeachNode extends Node {
  public $expression = null;    
  public $assignment = null;    
  public $statements = null;    
}
