<?php namespace xp\compiler\ast;

/**
 * Try
 */
class TryNode extends Node {
  public $statements = null;
  public $handling   = [];  
}
