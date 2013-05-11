<?php namespace xp\compiler\types;

/**
 * Represents a constructor
 *
 * @see      xp://xp.compiler.types.Types
 */
class Constructor extends \lang\Object {
  public
    $modifiers  = 0,
    $parameters = array(),
    $holder     = null;

  /**
   * Creates a string representation of this method
   *
   * @return  string
   */
  public function toString() {
    $signature= '';
    foreach ($this->parameters as $parameter) {
      $signature.= ', '.$parameter->compoundName();
    }
    return sprintf(
      '%s<%s __construct(%s)>',
      $this->getClassName(),
      implode(' ', Modifiers::namesOf($this->modifiers)),
      substr($signature, 2)
    );
  }
}
