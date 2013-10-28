<?php namespace xp\compiler\diagnostic;

use xp\compiler\io\Source;

/**
 * Classes implementing this interface listen to the compilation 
 * process.
 *
 */
interface DiagnosticListener {

  /**
   * Called when compilation starts
   *
   * @param   xp.compiler.io.Source
   */
  public function compilationStarted(Source $src);

  /**
   * Called when a compilation is skipped.
   *
   * @param   xp.compiler.io.Source src
   * @param   io.File compiled
   * @param   string[] messages
   */
  public function compilationSkipped(Source $src, \io\File $compiled, array $messages= array());

  /**
   * Called when a compilation finishes successfully.
   *
   * @param   xp.compiler.io.Source
   * @param   io.File compiled
   * @param   string[] messages
   */
  public function compilationSucceeded(Source $src, \io\File $compiled, array $messages= array());
  
  /**
   * Called when parsing fails
   *
   * @param   xp.compiler.io.Source
   * @param   text.parser.generic.ParseException reason
   */
  public function parsingFailed(Source $src, \text\parser\generic\ParseException $reason);

  /**
   * Called when emitting fails
   *
   * @param   xp.compiler.io.Source
   * @param   lang.FormatException reason
   */
  public function emittingFailed(Source $src, \lang\FormatException $reason);

  /**
   * Called when compilation fails
   *
   * @param   xp.compiler.io.Source
   * @param   lang.Throwable reason
   */
  public function compilationFailed(Source $src, \lang\Throwable $reason);

  /**
   * Called when a run starts.
   *
   */
  public function runStarted();
  
  /**
   * Called when a test run finishes.
   *
   */
  public function runFinished();
}