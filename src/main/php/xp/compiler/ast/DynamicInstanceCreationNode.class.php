<?php namespace xp\compiler\ast;

/**
 * Dynamic instance creation
 *
 * Example
 * ~~~~~~~
 * ```php
 * $a= new $type();
 * ```
 *
 * Note
 * ~~~~
 * This is only available in PHP syntax!
 *
 * @see   xp://xp.compiler.ast.InstanceCreationNode
 * @see   php://new
 */
class DynamicInstanceCreationNode extends Node {
  public $parameters = null;
  public $variable = '';
}
