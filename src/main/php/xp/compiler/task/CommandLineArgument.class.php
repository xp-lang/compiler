<?php namespace xp\compiler\task;

use xp\compiler\Syntax;

/**
 * Source implementation based on command line input
 */
class CommandLineArgument extends \lang\Object implements Argument {
  protected $syntax= null;
  protected $fragment= '';
  protected $return= false;

  /**
   * Constructor
   *
   * @param   string syntax
   * @param   string fragment
   * @param   bool return whether to add return statement if not present in fragment
   * @throws  lang.IllegalArgumentException
   */
  public function __construct($syntax, $fragment, $return= false) {
    $this->syntax= \xp\compiler\Syntax::forName($syntax);
    $this->fragment= $fragment;
    $this->return= $return;
  }
  
  /**
   * Get sources
   *
   * @return  xp.compiler.io.Source
   */
  public function getSources() {
    return array(new \xp\compiler\io\CommandLineSource($this->syntax, $this->fragment, $this->return));
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->fragment.' @ '.$this->syntax->toString().'>';
  }
}