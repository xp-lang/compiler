<?php namespace xp\compiler\types;

/**
 * Represents a constant
 *
 * @see      xp://xp.compiler.types.Types
 */
class Constant extends \lang\Object {
  public
    $name       = '',
    $type       = null,
    $value      = null;

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
   * Creates a string representation of this field
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      '%s<const %s %s= %s>',
      $this->getClassName(),
      $this->type->compoundName(),
      $this->name,
      \xp::stringOf($this->value)
    );
  }
}
