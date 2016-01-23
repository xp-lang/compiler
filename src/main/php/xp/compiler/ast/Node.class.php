<?php namespace xp\compiler\ast;

/**
 * Base class for all nodes
 */
abstract class Node extends \lang\Object {
  public $position = [0, 0];

  /**
   * Constructor
   *
   * @param   [:var] members default array()
   */
  public function __construct($members= []) {
    foreach ($members as $name => $value) {
      $this->{$name}= $value;
    }
  }
  
  /**
   * Helper method to compare two objects (as arrays) recursively
   *
   * FIXME: This can be replaced by util.Objects::equal() - but we need
   * to bump our minimum requirement to 5.9.2
   *
   * @param   array a1
   * @param   array a2
   * @return  bool
   */
  protected function memberWiseCompare($a1, $a2) {
    foreach (array_keys((array)$a1) as $k) {
      if ('position' === $k || 'holder' === $k || '__id' === $k) continue;

      switch (true) {
        case !array_key_exists($k, $a2): 
          return false;

        case is_array($a1[$k]):
          if (!$this->memberWiseCompare($a1[$k], $a2[$k])) return false;
          break;

        case $a1[$k] instanceof \lang\Generic:
          if (!$a1[$k]->equals($a2[$k])) return false;
          break;

        case $a1[$k] !== $a2[$k]:
          return false;
      }
    }
    return true;
  }
      
  /**
   * Returns whether an object is equal to this node.
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->memberWiseCompare((array)$this, (array)$cmp);
  }

  /**
   * Creates a string representation of this node.
   *
   * @return  string
   */
  public function toString() {
    $s= nameof($this).'(line '.$this->position[0].', offset '.$this->position[1].")@{\n";
    foreach (get_object_vars($this) as $name => $value) {
      '__id' !== $name && 'position' !== $name && 'holder' !== $name && $s.= sprintf(
        "  [%-20s] %s\n", 
        $name, 
        str_replace("\n", "\n  ", \xp::stringOf($value))
      );
    }
    return $s.'}';
  }
}
