<?php namespace xp\compiler\ast;

/**
 * Represents an interface declaration
 *
 */
class InterfaceNode extends TypeDeclarationNode {
  public $parents= null;

  /**
   * Constructor
   *
   * @param   int modifiers
   * @param   xp.compiler.ast.AnnotationNode[] annotations
   * @param   xp.compiler.types.TypeName name
   * @param   xp.compiler.types.TypeName[] parents
   * @param   xp.compiler.ast.Node[] body
   */
  public function __construct($modifiers= 0, array $annotations= null, TypeName $name= null, array $parents= null, array $body= null) {
    $this->modifiers= $modifiers;
    $this->annotations= $annotations;
    $this->name= $name;
    $this->parents= $parents;
    $this->setBody($body);
  }
}
