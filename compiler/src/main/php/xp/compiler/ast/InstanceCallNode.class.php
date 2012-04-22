<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');
  
  /**
   * Represents an instance call
   *
   * <code>
   *   $closure.();
   *   $operation.(1, 2);
   * </code>
   *
   * @see   php://call_user_func
   */
  class InstanceCallNode extends xp·compiler·ast·Node {
    public $target= NULL;
    public $arguments= array();
    public $nav= FALSE;
    
    /**
     * Creates a new InstanceCallNode object
     *
     * @param   xp.compiler.ast.Node target
     * @param   xp.compiler.ast.Node[] arguments
     * @param   bool nav
     */
    public function __construct($target= NULL, $arguments= NULL, $nav= FALSE) {
      $this->target= $target;
      $this->arguments= $arguments;
      $this->nav= $nav;
    }
  }
?>
