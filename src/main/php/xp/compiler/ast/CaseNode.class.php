<?php namespace xp\compiler\ast;

/**
 * Switch statement: Case
 *
 * @see  xp://xp.compiler.SwitchNode
 */
class CaseNode extends Node {
  public
    $expression,
    $statements;
}