<?php namespace xp\compiler\ast;

/**
 * Ternary operator `condition ? expression : conditional`
 */
class TernaryNode extends Node {
  public
    $condition,
    $expression,
    $conditional;  
}
