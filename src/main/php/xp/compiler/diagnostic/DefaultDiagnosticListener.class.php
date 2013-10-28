<?php namespace xp\compiler\diagnostic;

use xp\compiler\io\Source;

/**
 * Default DiagnosticListener implementation
 *
 * @see   xp://xp.compiler.diagnostic.DiagnosticListener
 */
class DefaultDiagnosticListener extends \lang\Object implements DiagnosticListener {
  protected
    $writer    = null,
    $started   = 0,
    $failed    = 0,
    $succeeded = 0,
    $timer     = null,
    $messages  = array();
  
  /**
   * Constructor
   *
   * @param   io.streams.OutputStreamWriter writer
   */
  public function __construct(\io\streams\OutputStreamWriter $writer) {
    $this->writer= $writer;
    $this->timer= new \util\profiling\Timer();
  }

  /**
   * Called when compilation starts
   *
   * @param   xp.compiler.io.Source src
   */
  public function compilationStarted(Source $src) {
    $this->started++;
  }

  /**
   * Called when a compilation is skipped.
   *
   * @param   xp.compiler.io.Source src
   * @param   io.File compiled
   * @param   string[] messages
   */
  public function compilationSkipped(Source $src, \io\File $compiled, array $messages= array()) {
    $this->writer->write('S');
    if (!empty($messages)) {
      $this->messages[$src->getURI()]= $messages;
    }
  }

  /**
   * Called when a compilation finishes successfully.
   *
   * @param   xp.compiler.io.Source src
   * @param   io.File compiled
   * @param   string[] messages
   */
  public function compilationSucceeded(Source $src, \io\File $compiled, array $messages= array()) {
    $this->writer->write('.');
    $this->succeeded++;
    if (!empty($messages)) {
      $this->messages[$src->getURI()]= $messages;
    }
  }
  
  /**
   * Called when parsing fails
   *
   * @param   xp.compiler.io.Source src
   * @param   text.parser.generic.ParseException reason
   */
  public function parsingFailed(Source $src, \text\parser\generic\ParseException $reason) {
    $this->writer->write('P');
    $this->failed++;
    $this->messages[$src->getURI()]= $reason->getCause()->compoundMessage();
  }

  /**
   * Called when emitting fails
   *
   * @param   xp.compiler.io.Source src
   * @param   lang.FormatException reason
   */
  public function emittingFailed(Source $src, \lang\FormatException $reason) {
    $this->writer->write('E');
    $this->failed++;
    $this->messages[$src->getURI()]= $reason->compoundMessage();
  }

  /**
   * Called when compilation fails
   *
   * @param   xp.compiler.io.Source src
   * @param   lang.Throwable reason
   */
  public function compilationFailed(Source $src, \lang\Throwable $reason) {
    $this->writer->write('F');
    $this->failed++;
    $this->messages[$src->getURI()]= $reason->compoundMessage();
  }

  /**
   * Called when a run starts.
   *
   */
  public function runStarted() {
    $this->failed= $this->succeeded= $this->started= 0;
    $this->writer->write('[');
    $this->timer->start();
    $this->messages= array();
  }
  
  /**
   * Called when a test run finishes.
   *
   */
  public function runFinished() {
    $this->timer->stop();
    $this->writer->writeLine(']');
    $this->writer->writeLine();
    
    if (!empty($this->messages)) {
      foreach ($this->messages as $uri => $message) {
        $this->writer->writeLine('* ', basename($uri), ': ', str_replace("\n", "\n  ", $message));
        $this->writer->writeLine();
      }
    }
    
    // Summary
    $this->writer->writeLinef('Done: %d/%d compiled, %d failed', $this->succeeded, $this->started, $this->failed);
    $this->writer->writeLinef(
      'Memory used: %.2f kB (%.2f kB peak)',
      memory_get_usage(true) / 1024,
      memory_get_peak_usage(true) / 1024
    );
    $this->writer->writeLinef(
      'Time taken: %.2f seconds (%.3f avg)',
      $this->timer->elapsedTime(),
      $this->timer->elapsedTime() / $this->started
    );
  }
}
