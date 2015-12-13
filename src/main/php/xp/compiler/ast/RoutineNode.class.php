<?php namespace xp\compiler\ast;

/**
 * Abstract base class for all routines
 *
 * @see   xp://xp.compiler.ast.MethodNode
 * @see   xp://xp.compiler.ast.ConstructorNode
 * @see   xp://xp.compiler.ast.OperatorNode
 */
abstract class RoutineNode extends TypeMemberNode {
  public $comment    = null;
  public $body       = null;
  public $parameters = [];
  public $throws     = [];
  
  /**
   * Adds a statement
   *
   * @param   xp.compiler.types.Node
   */
  public function addStatement(Node $statement) {
    $this->body[]= $statement;
  }

  /**
   * Returns this members's hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return $this->getName().'()';
  }
}