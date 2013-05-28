<?php namespace xp\compiler\ast;

/**
 * The "if" statement
 */
class IfNode extends Node {
  public $condition  = null;
  public $statements = null;
  public $otherwise  = null;  
}
