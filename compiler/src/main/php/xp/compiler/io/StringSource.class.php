<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.io.Source', 'io.streams.MemoryInputStream');

  /**
   * Source implementation
   *
   */
  class StringSource extends Object implements xp·compiler·io·Source {
    protected $source= NULL;
    protected $name= NULL;
    protected $syntax= NULL;
    
    /**
     * Constructor
     *
     * @param   string source
     * @param   xp.compiler.Syntax s Syntax to use
     */
    public function __construct($source, Syntax $syntax, $name= NULL) {
      $this->source= $source;
      $this->syntax= $syntax;
      $this->name= NULL === $name ? 'Compiled source #'.crc32($source) : $name;
    }
    
    /**
     * Get input stream
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream() {
      return new MemoryInputStream($this->source);
    }
    
    /**
     * Get syntax
     *
     * @return  xp.compiler.Syntax
     */
    public function getSyntax() {
      return $this->syntax;
    }

    /**
     * Get URI of this source - as source in error messages and
     * warnings.
     *
     * @return  string
     */
    public function getURI() {
      return $this->name;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->name.'>';
    }

    /**
     * Creates a hashcode of this object
     *
     * @return  string
     */
    public function hashCode() {
      return 'S:'.$this->name;
    }
  }
?>
