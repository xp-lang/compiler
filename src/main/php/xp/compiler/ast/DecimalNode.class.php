<?php namespace xp\compiler\ast;

/**
 * Represents a decimal literal
 *
 * @test  xp://net.xp_lang.tests.resolve.NumberResolveTest
 */
class DecimalNode extends NumberNode {

  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    return (double)$this->value;
  }
}
