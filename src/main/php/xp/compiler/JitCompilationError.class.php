<?php namespace xp\compiler;

class JitCompilationError extends \lang\ClassLinkageException {
  protected $format;

  /**
   * Constructor
   *
   * @param  string $class
   * @param  lang.IClassLoader[] $loaders
   * @param  string[] $messages
   * @param  lang.Throwable $cause
   */
  public function __construct($class, $loaders, $messages, \lang\Throwable $cause= null) {
    $this->format= "Cannot compile class %s: {\n";
    foreach ($messages as $message) {
      $this->format.= "  - ".substr($message, 0, strcspn($message, "\n"))."\n";
    }
    $this->format.= "}";
    parent::__construct($class, $loaders, $cause);
  }

  /**
   * Returns the exception's message format string.
   *
   * @return  string
   */
  protected function message() {
    return $this->format;
  }
}