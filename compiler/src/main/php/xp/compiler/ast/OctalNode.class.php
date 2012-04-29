<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.NaturalNode');

  /**
   * Represents an octal literal
   *
   * @see   xp://xp.compiler.ast.NaturalNode
   * @test  xp://net.xp_lang.tests.resolve.NumberResolveTest
   */
  class OctalNode extends NaturalNode {
    
    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      return octdec($this->value);
    }
  }
?>
