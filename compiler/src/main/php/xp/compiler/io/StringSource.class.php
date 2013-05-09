<?php

  uses('xp.compiler.io.Source', 'io.streams.MemoryInputStream');

  /**
   * Source implementation
   *
   * @test    xp://net.xp_lang.tests.StringSourceTest
   */
  class StringSource extends \lang\Object implements xp·compiler·io·Source {
    protected $source= null;
    protected $name= null;
    protected $syntax= null;
    
    /**
     * Constructor
     *
     * @param   string source
     * @param   xp.compiler.Syntax s Syntax to use
     */
    public function __construct($source, Syntax $syntax, $name= null) {
      $this->source= $source;
      $this->syntax= $syntax;
      $this->name= null === $name ? 'Compiled source #'.crc32($source) : $name;
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
