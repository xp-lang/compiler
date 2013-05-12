<?php namespace xp\compiler\diagnostic;

use xp\compiler\io\Source;

/**
 * Quiet DiagnosticListener implementation. Shows only errors
 *
 * @see   xp://xp.compiler.diagnostic.DiagnosticListener
 */
class QuietDiagnosticListener extends \lang\Object implements DiagnosticListener {
  protected $writer= null;
  
  /**
   * Constructor
   *
   * @param   io.streams.OutputStreamWriter writer
   */
  public function __construct(\io\streams\OutputStreamWriter $writer) {
    $this->writer= $writer;
  }

  /**
   * Called when compilation starts
   *
   * @param   xp.compiler.io.Source src
   */
  public function compilationStarted(Source $src) {
    // NOOP
  }

  /**
   * Called when a compilation finishes successfully.
   *
   * @param   xp.compiler.io.Source src
   * @param   io.File compiled
   * @param   string[] messages
   */
  public function compilationSucceeded(Source $src, \io\File $compiled, array $messages= array()) {
    // NOOP
  }
  
  /**
   * Called when parsing fails
   *
   * @param   xp.compiler.io.Source src
   * @param   text.parser.generic.ParseException reason
   */
  public function parsingFailed(Source $src, \text\parser\generic\ParseException $reason) {
    $this->writer->write($src, ': ', $reason);
  }

  /**
   * Called when emitting fails
   *
   * @param   xp.compiler.io.Source src
   * @param   lang.FormatException reason
   */
  public function emittingFailed(Source $src, \lang\FormatException $reason) {
    $this->writer->write($src, ': ', $reason);
  }

  /**
   * Called when compilation fails
   *
   * @param   xp.compiler.io.Source src
   * @param   lang.Throwable reason
   */
  public function compilationFailed(Source $src, \lang\Throwable $reason) {
    $this->writer->write($src, ': ', $reason);
  }

  /**
   * Called when a run starts.
   */
  public function runStarted() {
    // NOOP
  }
  
  /**
   * Called when a test run finishes.
   */
  public function runFinished() {
    // NOOP
  }
}
