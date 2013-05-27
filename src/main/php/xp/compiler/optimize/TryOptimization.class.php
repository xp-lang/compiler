<?php namespace xp\compiler\optimize;

use xp\compiler\ast\TryNode;
use xp\compiler\ast\CatchNode;
use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\ThrowNode;
use xp\compiler\ast\StatementsNode;
use xp\compiler\ast\NoopNode;

/**
 * Optimizes try / catch blocks
 *
 * @test     xp://tests.optimization.TryOptimizationTest
 */
class TryOptimization extends \lang\Object implements Optimization {
  
  /**
   * Return node this optimization works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.TryNode');
  }
  
  /**
   * Optimize a given node
   *
   * @param   xp.compiler.ast.Node in
   * @param   xp.compiler.types.Scope scope
   * @param   xp.compiler.optimize.Optimizations optimizations
   * @param   xp.compiler.ast.Node optimized
   */
  public function optimize(\xp\compiler\ast\Node $in, \xp\compiler\types\Scope $scope, Optimizations $optimizations) {
    $try= cast($in, 'xp.compiler.ast.TryNode');
    
    // try { } catch (... $e) { ... } => NOOP
    // try { } finally { ..[1].. }    => [1]
    if (0 === sizeof($try->statements)) {
      $last= $try->handling[sizeof($try->handling)- 1];
      if ($last instanceof FinallyNode) {
        return new StatementsNode($last->statements);
      } else {
        return new NoopNode();
      }
    }
    
    // try { ..[1].. } catch (... $e) { throw $e; } => [1]
    if (
      1 === sizeof($try->handling) && 
      $try->handling[0] instanceof CatchNode &&
      1 === sizeof($try->handling[0]->statements) &&
      $try->handling[0]->statements[0] instanceof ThrowNode &&
      $try->handling[0]->variable === $try->handling[0]->statements[0]->expression->name
    ) {
      return new StatementsNode($try->statements);
    }

    return $in;
  }
}
