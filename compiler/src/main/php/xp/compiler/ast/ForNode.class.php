<?php namespace xp\compiler\ast;

/**
 * The "for" loop
 */
class ForNode extends Node {
  public $initialization = null;    
  public $condition      = null;    
  public $loop           = null;    
  public $statements     = null;    
  
}
