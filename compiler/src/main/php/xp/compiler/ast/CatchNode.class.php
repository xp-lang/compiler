<?php namespace xp\compiler\ast;

/**
 * Try: Catch
 *
 * @see   xp://xp.compiler.ast.TryNode
 */
class CatchNode extends Node {
  public $type       = null;
  public $variable   = null;
  public $statements = null;  
}
