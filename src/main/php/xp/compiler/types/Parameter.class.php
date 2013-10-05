<?php namespace xp\compiler\types;

use xp\compiler\ast\Node;

/**
 * Represents a method's parameter
 *
 * @see      xp://xp.compiler.types.Types
 */
class Parameter extends \lang\Object {
  public
    $name       = '',
    $type       = null,
    $default    = null;

  /**
   * Constructor
   *
   * @param   string $name
   * @param   xp.compiler.types.TypeName $type
   * @param   xp.compiler.ast.Node $default
   */
  public function __construct($name, $type, Node $default= null) {
    $this->name= $name;
    $this->type= $type;
    $this->default= $default;
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
   * Returns whether this parameter is optional
   *
   * @return  bool
   */
  public function isOptional() {
    return null === $this->default;
  }

  /**
   * Returns whether a given value is equal to this
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return (
      $cmp instanceof self &&
      $this->name === $cmp->name &&
      $this->type->equals($cmp->type) &&
      (null === $this->default && null === $cmp->default || $this->default->equals($cmp->default))
    );
  }
  
  /**
   * Creates a string representation of this field
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      '%s<%s %s%s>',
      $this->getClassName(),
      $this->type->compoundName(),
      $this->name,
      $this->default ? '= '.$this->default->hashCode() : ''
    );
  }
}