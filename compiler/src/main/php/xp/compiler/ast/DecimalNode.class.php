<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.NumberNode');

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
?>
