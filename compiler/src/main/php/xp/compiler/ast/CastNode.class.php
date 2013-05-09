<?php namespace xp\compiler\ast;

/**
 * Cast
 */
class CastNode extends Node {
  public $type       = null;
  public $expression = null;
  public $check      = true;
}
