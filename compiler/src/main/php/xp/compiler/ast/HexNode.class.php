<?php namespace xp\compiler\ast;

/**
 * Represents a hex literal
 *
 * @see   xp://xp.compiler.ast.NaturalNode
 * @test  xp://net.xp_lang.tests.resolve.NumberResolveTest
 */
class HexNode extends NaturalNode {
  
  /**
   * Resolve this node's value.
   *
   * @return  var
   */
  public function resolve() {
    return hexdec($this->value);
  }
}
