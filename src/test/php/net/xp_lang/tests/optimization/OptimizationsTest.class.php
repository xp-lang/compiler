<?php namespace net\xp_lang\tests\optimization;

use xp\compiler\optimize\Optimizations;
use xp\compiler\types\MethodScope;
use xp\compiler\ast\StringNode;

/**
 * TestCase for Optimizations class
 *
 * @see   xp://xp.compiler.optimize.Optimizations
 */
class OptimizationsTest extends \unittest\TestCase {
  protected static $optimization;
  protected $fixture= null;
  protected $scope= null;
  
  static function __static() {
    self::$optimization= newinstance('xp.compiler.optimize.Optimization', array(), '{
      public function node() { 
        return \lang\XPClass::forName("xp.compiler.ast.StringNode"); 
      }

      public function optimize(\xp\compiler\ast\Node $in, \xp\compiler\types\Scope $scope, Optimizations $optimizations) {
        return new \xp\compiler\ast\StringNode("Optimized: ".$in->value);
      }
    }');
  }

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new Optimizations();
    $this->scope= new MethodScope();
  }
  
  /**
   * Test optimize()
   */
  #[@test]
  public function withoutOptimization() {
    $this->assertEquals(
      new StringNode('Test'), 
      $this->fixture->optimize(new StringNode('Test'), $this->scope)
    );
  }
  
  /**
   * Tests add() and optimize()
   */
  #[@test]
  public function withOptimization() {
    $this->fixture->add(self::$optimization);
    $this->assertEquals(
      new StringNode('Optimized: Test'), 
      $this->fixture->optimize(new StringNode('Test'), $this->scope)
    );
  }

  /**
   * Test clear() and optimize()
   */
  #[@test]
  public function clearOptimizations() {
    $this->fixture->add(self::$optimization);
    $this->fixture->clear();
    $this->assertEquals(
      new StringNode('Test'), 
      $this->fixture->optimize(new StringNode('Test'), $this->scope)
    );
  }
}
