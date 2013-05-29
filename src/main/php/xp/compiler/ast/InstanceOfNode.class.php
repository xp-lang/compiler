<?php namespace xp\compiler\ast;

/**
 * Instanceof statement
 */
class InstanceOfNode extends Node {
  public $type = null;
  public $expression = null;
}
