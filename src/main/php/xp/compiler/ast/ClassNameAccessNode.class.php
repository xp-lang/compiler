<?php namespace xp\compiler\ast;

/**
 * Represents class name access
 *
 * ```php
 * self::class;
 * ```
 */
class ClassNameAccessNode extends Node {
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
