<?php namespace xp\compiler\types;

/**
 * Represents an operator
 *
 * @see      xp://xp.compiler.types.Types
 */
class Operator extends \lang\Object {
  public
    $symbol     = '',
    $returns    = null,
    $modifiers  = 0,
    $parameters = [],
    $holder     = null;

  /**
   * Constructor
   *
   * @param   string symbol
   */
  public function __construct($symbol= '') {
    $this->symbol= $symbol;
  }

  /**
   * Returns name
   *
   * @return  string
   */
  public function name() {
    return $this->symbol;
  }

  /**
   * Creates a string representation of this Operator
   *
   * @return  string
   */
  public function toString() {
    $signature= '';
    foreach ($this->parameters as $parameter) {
      $signature.= ', '.$parameter->compoundName();
    }
    return sprintf(
      '%s<%s %s %s(%s)>',
      nameof($this),
      implode(' ', \lang\reflect\Modifiers::namesOf($this->modifiers)),
      $this->returns->compoundName(),
      $this->symbol,
      substr($signature, 2)
    );
  }
}
