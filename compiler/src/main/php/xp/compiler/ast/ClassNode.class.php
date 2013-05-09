<?php namespace xp\compiler\ast;

/**
 * Represents a class declaration
 */
class ClassNode extends TypeDeclarationNode {
  public $parent= null;
  public $implements= null;
  public $literal= null;
  
  /**
   * Constructor
   *
   * @param   int modifiers
   * @param   xp.compiler.ast.AnnotationNode[] annotations
   * @param   xp.compiler.types.TypeName name
   * @param   xp.compiler.types.TypeName parent
   * @param   xp.compiler.types.TypeName[] implements
   * @param   xp.compiler.ast.Node[] body
   */
  public function __construct($modifiers= 0, array $annotations= null, TypeName $name= null, TypeName $parent= null, array $implements= null, array $body= null) {
    $this->modifiers= $modifiers;
    $this->annotations= $annotations;
    $this->name= $name;
    $this->parent= $parent;
    $this->implements= $implements;
    $this->setBody($body);
  }
}
