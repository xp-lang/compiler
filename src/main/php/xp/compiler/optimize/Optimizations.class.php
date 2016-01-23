<?php namespace xp\compiler\optimize;

use util\collections\HashTable;

/**
 * Optimizer API
 *
 * @test    xp://net.xp_lang.tests.optimization.OptimizationsTest
 * @see     http://www.compileroptimizations.com/
 * @see     http://en.wikipedia.org/wiki/Compiler_optimization
 */
class Optimizations extends \lang\Object {
  protected $impl= null;

  /**
   * Constructor.
   *
   */
  public function __construct() {
    $this->impl= new HashTable();
  }
  
  /**
   * Add an optimization implementation
   *
   * @param   xp.compiler.optimize.Optimization impl
   */
  public function add(Optimization $impl) {
    $this->impl[$impl->node()]= $impl;
  }

  /**
   * Clear all implementations
   *
   */
  public function clear() {
    $this->impl->clear();
  }
  
  /**
   * Optimize a given node
   *
   * @param   xp.compiler.ast.Node in
   * @param   xp.compiler.types.Scope scope
   * @param   xp.compiler.ast.Node optimized
   */
  public function optimize(\xp\compiler\ast\Node $in, \xp\compiler\types\Scope $scope) {
    $key= $in->getClass();
    if (!$this->impl->containsKey($key)) {
      return $in;
    } else {
      return $this->impl[$key]->optimize($in, $scope, $this);
    }
  }
  
  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    $s= nameof($this).'('.$this->impl->size().")@{\n";
    foreach ($this->impl->keys() as $key) {
      $s.= sprintf("  [%-20s] %s\n", $key->getSimpleName(), nameof($this->impl->get($key)));
    }
    return $s.'}';
  }
}
