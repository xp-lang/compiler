<?php namespace xp\compiler\ast;

/**
 * Represents a class constant
 *
 * ```php
 * class HttpMethods {
 *   const string GET  = 'GET';
 *   const string POST = 'POST';
 *   const string HEAD = 'HEAD';
 * }
 * 
 * $get= HttpMethods::GET;
 * ```
 *
 * Class constants are limited to numbers, booleans and strings
 * but provide a cheap way of extracting magic constants from 
 * business logic. If you require more flexibility, use static 
 * fields.
 *
 * @see   xp://xp.compiler.ast.FieldNode
 */
class ClassConstantNode extends TypeMemberNode {
  public $type= null;
  public $value= null;

  /**
   * Constructor
   *
   * @param   xp.compiler.ast.Node expression
   * @param   xp.compiler.ast.Node[] statements
   */
  public function __construct($name, \xp\compiler\types\TypeName $type, Node $value) {
    $this->name= $name;
    $this->type= $type;
    $this->value= $value;
  }

  /**
   * Returns this members's hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return $this->getName();
  }
}
