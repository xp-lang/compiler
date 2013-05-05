<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.InputStream');

  /**
   * A source class
   *
   */
  class SourceClass extends Object {
    protected $name= '';
    protected $stream= NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   io.streams.InputStream stream
     */
    public function __construct($name, InputStream $stream) {
      $this->name= $name;
      $this->stream= $stream;
    }

    /**
     * Returns name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Returns input stream
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream() {
      return $this->stream;
    }
  }
?>
