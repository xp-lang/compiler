<?php namespace xp\compiler\checks;

/**
 * Check whether api documentation is available for a type members, 
 * that is: methods and constructors.
 */
class TypeMemberHasDocumentation extends \lang\Object implements Check {

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
    $member= \cast($node, 'xp.compiler.ast.RoutineNode');
    if (!isset($member->comment) && !$scope->declarations[0]->synthetic) {
      return array('D201', 'No api doc for member '.$scope->declarations[0]->name->compoundName().'::'.$member->getName());
    }
  }
}