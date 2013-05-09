<?php namespace xp\compiler\ast;

/**
 * Unary operator
 */
class UnaryOpNode extends Node {
  public $postfix = false;
  public $expression = null;
  public $op = null;
}
