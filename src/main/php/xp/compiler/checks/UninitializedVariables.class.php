<?php namespace xp\compiler\checks;

/**
 * Check for unitialized variables
 *
 */
class UninitializedVariables extends \lang\Object implements Check {

  /**
   * Return node this check works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.VariableNode');
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
    $v= \cast($node, 'xp.compiler.ast.VariableNode');
    if (!$scope->getType($v)) {
      return ['V404', 'Uninitialized variable '.$v->name];
    }
  }
}
