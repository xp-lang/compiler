<?php namespace xp\compiler\types;

/**
 * Represents a method
 *
 * @see      xp://xp.compiler.types.Types
 */
class Method extends \lang\Object {
  public
    $name       = '',
    $returns    = null,
    $modifiers  = 0,
    $parameters = array(),
    $holder     = null;

  /**
   * Constructor
   *
   * @param   string name
   */
  public function __construct($name= '') {
    $this->name= $name;
  }

  /**
   * Returns name
   *
   * @return  string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Creates a string representation of this method
   *
   * @return  string
   */
  public function toString() {
    $signature= '';
    foreach ($this->parameters as $parameter) {
      $signature.= ', '.$parameter->toString();
    }
    return sprintf(
      '%s<%s %s %s(%s)>',
      $this->getClassName(),
      implode(' ', \lang\reflect\Modifiers::namesOf($this->modifiers)),
      $this->returns->compoundName(),
      $this->name,
      substr($signature, 2)
    );
  }
}
