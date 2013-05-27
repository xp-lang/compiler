<?php namespace xp\compiler\ast;

/**
 * Switch statement
 */
class SwitchNode extends Node {
  public $expression = null;
  public $cases      = array();
}
