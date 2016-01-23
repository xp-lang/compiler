<?php namespace xp\compiler\ast;

/**
 * Represents a type member
 *
 * @see      xp://xp.compiler.ast.RoutineNode
 * @see      xp://xp.compiler.ast.FieldNode
 * @see      xp://xp.compiler.ast.EnumMemberNode
 */
abstract class TypeMemberNode extends Node {
  public $name= '';
  public $modifiers= 0;
  public $annotations= [];
  public $comment= null;

  /**
   * Returns this routine's name
   *
   * @return  string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Returns this members's hashcode
   *
   * @return  string
   */
  public function hashCode() {
    raise('lang.MethodNotImplementedException', 'Not implemented', __METHOD__);
  }
}