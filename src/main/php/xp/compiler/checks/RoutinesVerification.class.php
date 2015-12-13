<?php namespace xp\compiler\checks;

use lang\reflect\Modifiers;

/**
 * Verifies routines
 *
 * @test    xp://net.xp_lang.tests.checks.RoutinesVerificationTest
 */
class RoutinesVerification extends \lang\Object implements Check {

  /**
   * Return node this check works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.RoutineNode');
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
    $routine= \cast($node, 'xp.compiler.ast.RoutineNode');

    $qname= $scope->declarations[0]->name->compoundName().'::'.$routine->getName();
    $empty= $routine->body === null;
    if ($scope->declarations[0] instanceof \xp\compiler\ast\InterfaceNode) {
      if (!$empty) {
        return ['R403', 'Interface methods may not have a body '.$qname];
      } else if ($routine->modifiers !== MODIFIER_PUBLIC && $routine->modifiers !== 0) {
        return ['R401', 'Interface methods may only be public '.$qname];
      }
    } else {
      if (Modifiers::isAbstract($routine->modifiers) && !$empty) {
        return ['R403', 'Abstract methods may not have a body '.$qname];
      } else if (!Modifiers::isAbstract($routine->modifiers) && $empty) {
        return ['R401', 'Non-abstract methods must have a body '.$qname];
      }
      if ($routine->extension && !Modifiers::isStatic($routine->modifiers)) {
        return ['E403', 'Extension methods must be static '.$qname];
      }
    }
  }
}
