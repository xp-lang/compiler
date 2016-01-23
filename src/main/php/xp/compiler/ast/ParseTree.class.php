<?php namespace xp\compiler\ast;

/**
 * A parse tree is the result of the parsing processs
 */
class ParseTree extends \lang\Object {
  public
    $package,
    $imports,
    $declaration,
    $origin;

  /**
   * Constructor
   *
   * @param   string package
   * @param   xp.compiler.ast.Node[] imports
   * @param   xp.compiler.ast.TypeDeclarationNode declaration
   */
  public function __construct($package= '', $imports= [], TypeDeclarationNode $declaration= null) {
    $this->package= $package;
    $this->imports= $imports;
    $this->declaration= $declaration;
  }

  /**
   * Creates a string representation of this node.
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      "%s(package %s)@{\n".
      "  imports     : %s\n".
      "  declaration : %s\n".
      "}",
      nameof($this), 
      $this->package ? $this->package->name : '<main>',
      str_replace("\n", "\n  ", \xp::stringOf($this->imports)),
      str_replace("\n", "\n  ", $this->declaration->toString())
    );
  }
}
