<?php namespace xp\compiler\ast;

/**
 * Represents a type declaration
 *
 * @see      xp://xp.compiler.ast.ClassNode
 * @see      xp://xp.compiler.ast.InterfaceNode
 * @see      xp://xp.compiler.ast.EnumNode
 */
abstract class TypeDeclarationNode extends Node {
  public $modifiers= 0;
  public $annotations= null;
  public $name= null;
  public $body= null;
  public $comment= null;
  public $synthetic= false;
  
  /**
   * Sets this type's body
   *
   * @param   xp.compiler.ast.Node[] body
   */
  public function setBody(array $body= null) {
    $this->body= $body;
  }
}