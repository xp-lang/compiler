<?php namespace xp\compiler\ast;

/**
 * Represents constant access
 *
 * ```php
 * self::SEPARATOR;
 * ```
 */
class ConstantAccessNode extends Node {
  public $type= null;
  public $name= '';
  
  /**
   * Constructor
   *
   * @param   xp.compiler.types.TypeName type
   * @param   string name
   */
  public function __construct($type= null, $name= '') {
    $this->type= $type;
    $this->name= $name;
  }
}
