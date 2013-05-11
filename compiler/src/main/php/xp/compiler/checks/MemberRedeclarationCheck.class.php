<?php namespace xp\compiler\checks;

/**
 * Verifies routines cannot be redeclared
 *
 * @test    xp://tests.checks.MemberRedeclarationCheckTest
 */
class MemberRedeclarationCheck extends \lang\Object implements Check {

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
    $type= \cast($node, 'xp.compiler.ast.TypeDeclarationNode');
    if (!$type->body) return;   // Short-circuit

    $index= array();
    $qname= $type->name->compoundName();
    foreach ($type->body as $member) {
      $key= $member->hashCode();
      if (isset($index[$key])) {
        return array('C409', 'Cannot redeclare '.$qname.'::'.$key);
      }
      $index[$key]= true;
    }
  }
}