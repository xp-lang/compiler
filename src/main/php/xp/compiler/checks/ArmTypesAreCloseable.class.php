<?php namespace xp\compiler\checks;

use xp\compiler\types\TypeReflection;
use xp\compiler\ast\ArmNode;
use lang\XPClass;

/**
 * Verifies ARM statements
 *
 * @test    xp://net.xp_lang.tests.checks.ArmTypesAreCloseableTest
 */
class ArmTypesAreCloseable extends \lang\Object implements Check {
  protected static $closeable= null;
  
  static function __static() {
    self::$closeable= new TypeReflection(XPClass::forName('lang.Closeable'));
  }

  /**
   * Return node this check works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return XPClass::forName('xp.compiler.ast.ArmNode');
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
    $arm= cast($node, 'xp.compiler.ast.ArmNode');
    foreach ($arm->initializations as $i => $init) {
      $type= $scope->resolveType($scope->typeOf($init), false);
      if (!$type->isSubclassOf(self::$closeable)) {
        return ['A403', 'Type '.$type->name().' for assignment #'.($i+ 1).' in ARM block is not closeable'];
      }
    }
  }
}
