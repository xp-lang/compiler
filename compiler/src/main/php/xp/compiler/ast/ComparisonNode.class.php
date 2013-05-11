<?php namespace xp\compiler\ast;

/**
 * Comparison `lhs op rhs`
 */
class ComparisonNode extends Node {
  public $lhs = null;    
  public $rhs = null;    
  public $op  = null;    
}
