<?php namespace xp\compiler\ast;

/**
 * Binary operator `lhs op rhs`
 */
class BinaryOpNode extends Node {
  public $lhs = null;    
  public $rhs = null;    
  public $op  = null;    
}
