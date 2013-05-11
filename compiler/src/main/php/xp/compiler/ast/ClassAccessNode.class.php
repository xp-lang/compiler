<?php namespace xp\compiler\ast;

/**
 * Represents class access
 *
 * ```php
 * self::class;
 * ```
 */
class ClassAccessNode extends Node {
  public $type= null;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.types.TypeName type
   */
  public function __construct($type= null) {
    $this->type= $type;
  }
}
