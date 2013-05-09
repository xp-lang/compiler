<?php namespace xp\compiler\checks;

/**
 * Verifies fields
 *
 * @test    xp://net.xp_lang.tests.checks.FieldsVerificationTest
 */
class FieldsVerification extends \lang\Object implements Check {

  /**
   * Return node this check works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.FieldNode');
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
    $field= \cast($node, 'xp.compiler.ast.FieldNode');

    if ($scope->declarations[0] instanceof \xp\compiler\ast\InterfaceNode) {
      return array('I403', 'Interfaces may not have field declarations');
    }
  }
}