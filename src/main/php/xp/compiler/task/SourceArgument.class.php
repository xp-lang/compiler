<?php namespace xp\compiler\task;

/**
 * Source implementation based on command line input
 */
class SourceArgument extends \lang\Object implements Argument {
  protected $source= null;

  /**
   * Constructor
   *
   * @param   xp.compiler.io.Source $source
   */
  public function __construct($source) {
    $this->source= $source;
  }
  
  /**
   * Get sources
   *
   * @return  xp.compiler.io.Source
   */
  public function getSources() {
    return array($this->source);
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->source->toString().'>';
  }
}