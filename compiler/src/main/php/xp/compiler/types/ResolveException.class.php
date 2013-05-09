<?php namespace xp\compiler\types;

/**
 * Indicates resolution of a name failed
 *
 */
class ResolveException extends \lang\XPException {
  protected $kind;
  
  /**
   * Constructor
   *
   * @param   string message
   * @param   int kind
   * @param   lang.Throwable cause
   */
  public function __construct($message, $kind, \lang\Throwable $cause) {
    parent::__construct($message, $cause);
    $this->kind= $kind;
  }
  
  /**
   * Get kind
   *
   * @return  int
   */
  public function getKind() {
    return $this->kind;
  }
}
