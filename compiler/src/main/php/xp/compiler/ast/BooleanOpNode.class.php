<?php namespace xp\compiler\ast;

/**
 * Boolean operator `lhs op rhs`
 */
class BooleanOpNode extends Node {
  public $lhs = null;    
  public $rhs = null;    
  public $op  = null;    
}