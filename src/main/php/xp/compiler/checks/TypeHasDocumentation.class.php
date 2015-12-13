<?php namespace xp\compiler\checks;

/**
 * Check whether api documentation is available for a type, that
 * is: interfaces, classes and enums.
 *
 * @test    xp://tests.checks.TypeHasDocumentationTest
 */
class TypeHasDocumentation extends \lang\Object implements Check {

  /**
   * Return node this check works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.TypeDeclarationNode');
  }

  /**
   * Return whether this check is to be run deferred
   *
   * @return  bool
   */
  public function defer() {
    return false;
  }
  
  /**
   * Executes this check
   *
   * @param   xp.compiler.ast.Node node
   * @param   xp.compiler.types.Scope scope
   * @return  bool
   */
  public function verify(\xp\compiler\ast\Node $node, \xp\compiler\types\Scope $scope) {
    $decl= \cast($node, 'xp.compiler.ast.TypeDeclarationNode');
    if (!isset($decl->comment) && !$decl->synthetic) {
      return ['D201', 'No api doc for type '.$decl->name->compoundName()];
    }
  }
}
