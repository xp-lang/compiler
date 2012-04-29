<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.NumberNode');

  /**
   * Represents a natural number
   *
   * @see   xp://xp.compiler.ast.IntegerNode
   * @see   xp://xp.compiler.ast.HexNode
   * @see   xp://xp.compiler.ast.OctalNode
   */
  abstract class NaturalNode extends NumberNode {

  }
?>
