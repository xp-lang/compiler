<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a constant
   *
   */
  class ConstantNode extends xp·compiler·ast·Node {
    public $name= NULL;

    /**
     * Creates a new constant value node with a given name
     *
     * @param   string name
     */
    public function __construct($name= NULL) {
      $this->name= $name;
    }
  
    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return $this->name;
    }
  }
?>
