<?php namespace xp\compiler\types;

/**
 * Represents an indexer
 *
 * @see      xp://xp.compiler.types.Types
 */
class Indexer extends \lang\Object {
  public
    $type      = null,
    $parameter = null,
    $holder    = null;

  /**
   * Creates a string representation of this method
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      '%s<%s this[%s]>',
      nameof($this),
      $this->type->compoundName(),
      $this->parameter->compoundName()
    );
  }
}
