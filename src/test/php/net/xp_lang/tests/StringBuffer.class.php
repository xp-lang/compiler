<?php namespace net\xp_lang\tests;

class StringBuffer extends \lang\Object implements \ArrayAccess {
  private $buffer= '';
  public $length;

  /**
   * Constructor with optional initial string, defaulting to empty
   *
   * @param  string $initial
   */
  public function __construct($initial= '') {
    $this->buffer= $initial;
    $this->length= strlen($this->buffer);
  }

  /**
   * Constructor with non-optional initial string
   *
   * @param  string $initial
   * @return self
   */
  public static function valueOf($initial) { return new self($initial); }


  /**
   * Append another string
   *
   * @param  string $string
   * @return self
   */
  public function append($string) { $this->buffer.= $string; return $this; }

  /**
   * Returns whether a given value is equal to this string
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) { return $cmp instanceof self && $cmp->buffer === $this->buffer; }


  public function __toString() { return $this->buffer; }

  public function offsetGet($pos) { return $this->buffer[$pos]; }
  public function offsetExists($pos) { return $pos < $this->length; }
  public function offsetSet($pos, $value) { /* TBI */ }
  public function offsetUnset($pos) { /* TBI */ }
}