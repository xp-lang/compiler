<?php namespace xp\compiler\checks;

/**
 * Verifies static method calls
 *
 * @test    xp://net.xp_lang.tests.checks.StaticMethodCallVerificationTest
 */
class StaticMethodCallVerification extends AbstractMethodCallVerification {

  /**
   * Return node this check works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.StaticMethodCallNode');
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
    $call= \cast($node, 'xp.compiler.ast.StaticMethodCallNode');
    return $this->verifyMethod($call->type, $call->name, $scope);
  }
}
