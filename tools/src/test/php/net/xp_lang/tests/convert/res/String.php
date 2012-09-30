<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A string
   *
   */
  class String extends Object {
    protected $buffer= '';

    /**
     * Constructor
     *
     * @param   string initial
     */
    public function __construct($initial= '') {
      $this->buffer= $initial;
    }

    /**
     * Returns the character at a given position
     *
     * @param   int i
     * @return  string
     */
    public function charAt($i) {
      return $this->buffer{$i};
    }
  }
?>
