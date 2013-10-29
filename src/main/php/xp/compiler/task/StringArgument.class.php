<?php namespace xp\compiler\task;

use xp\compiler\Syntax;
use xp\compiler\io\StringSource;

/**
 * Source implementation based on command line input
 */
class StringArgument extends \lang\Object implements Argument {
  protected $syntax= null;
  protected $fragment= '';

  /**
   * Constructor
   *
   * @param   string fragment
   * @param   xp.compiler.Syntax syntax
   * @param   string name
   */
  public function __construct($fragment, Syntax $syntax, $name) {
    $this->fragment= $fragment;
    $this->syntax= $syntax;
    $this->name= $name;
  }
  
  /**
   * Get sources
   *
   * @return  xp.compiler.io.Source
   */
  public function getSources() {
    return array(new StringSource($this->fragment, $this->syntax, $this->name));
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->name.' @ '.$this->syntax->toString().'>';
  }
}