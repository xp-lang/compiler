<?php namespace xp\compiler\checks;

use xp\compiler\types\TypeInstance;
use xp\compiler\types\TypeName;

/**
 * Verifies routines
 *
 * @test    xp://net.xp_lang.tests.checks.FieldsVerificationTest
 */
class ArrayAccessVerification extends \lang\Object implements Check {

  /**
   * Return node this check works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.ArrayAccessNode');
  }

  /**
   * Return whether this check is to be run deferred
   *
   * @return  bool
   */
  public function defer() {
    return true;
  }
  
  /**
   * Executes this check
   *
   * @param   xp.compiler.ast.Node node
   * @param   xp.compiler.types.Scope scope
   * @return  bool
   */
  public function verify(\xp\compiler\ast\Node $node, \xp\compiler\types\Scope $scope) {
    $access= \cast($node, 'xp.compiler.ast.ArrayAccessNode');

    $type= $scope->typeOf($access->target);
    $result= TypeName::$VAR;
    $message= null;
    if ($type->isArray()) {
      $result= $type->arrayComponentType();
    } else if ($type->isMap()) {
      $result= $type->mapComponentType();
    } else if ($type->isClass()) {
      $ptr= new TypeInstance($scope->resolveType($type));
      if ($ptr->hasIndexer()) {
        $result= $ptr->getIndexer()->type;
      } else {
        $message= array('T305', 'Type '.$ptr->name().' does not support offset access');
      }
    } else if ($type->isVariable()) {
      $message= array('T203', 'Array access (var)'.$access->hashCode().' verification deferred until runtime');
    } else if ('string' === $type->name) {
      $result= $type;
    } else {
      $message= array('T305', 'Using array-access on unsupported type '.$type->toString());
    }
    
    $scope->setType($access, $result);
    return $message;
  }
}
