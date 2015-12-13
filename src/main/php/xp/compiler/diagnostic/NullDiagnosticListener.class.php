<?php namespace xp\compiler\diagnostic;

use xp\compiler\io\Source;

/**
 * Default DiagnosticListener implementation
 *
 * @see   xp://xp.compiler.diagnostic.DiagnosticListener
 */
class NullDiagnosticListener extends \lang\Object implements DiagnosticListener {

  /**
   * Called when compilation starts
   *
   * @param   xp.compiler.io.Source src
   */
  public function compilationStarted(Source $src) {
  }

  /**
   * Called when a compilation finishes successfully.
   *
   * @param   xp.compiler.io.Source src
   * @param   io.File compiled
   * @param   string[] messages
   */
  public function compilationSucceeded(Source $src, \io\File $compiled, array $messages= []) {
  }
  
  /**
   * Called when parsing fails
   *
   * @param   xp.compiler.io.Source src
   * @param   text.parser.generic.ParseException reason
   */
  public function parsingFailed(Source $src, \text\parser\generic\ParseException $reason) {
  }

  /**
   * Called when emitting fails
   *
   * @param   xp.compiler.io.Source src
   * @param   lang.FormatException reason
   */
  public function emittingFailed(Source $src, \lang\FormatException $reason) {
  }

  /**
   * Called when compilation fails
   *
   * @param   xp.compiler.io.Source src
   * @param   lang.Throwable reason
   */
  public function compilationFailed(Source $src, \lang\Throwable $reason) {
  }

  /**
   * Called when a run starts.
   *
   */
  public function runStarted() {
  }
  
  /**
   * Called when a test run finishes.
   *
   */
  public function runFinished() {
  }
}
