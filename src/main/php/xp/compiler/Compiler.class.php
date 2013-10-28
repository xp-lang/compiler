<?php namespace xp\compiler;

use xp\compiler\emit\Emitter;
use xp\compiler\task\CompilationTask;
use xp\compiler\diagnostic\DiagnosticListener;
use xp\compiler\io\FileManager;

/**
 * Compiler
 *
 */
class Compiler extends \lang\Object implements \util\log\Traceable {
  protected $cat= null;

  /**
   * Compile a set of files
   *
   * @param   xp.compiler.task.Argument[] sources
   * @param   xp.compiler.diagnostic.DiagnosticListener listener
   * @param   xp.compiler.io.FileManager manager
   * @param   xp.compiler.emit.Emitter emitter
   * @return  bool success if all files compiled correctly, true, false otherwise
   */
  public function compile(array $args, DiagnosticListener $listener, FileManager $manager, Emitter $emitter) {
    $emitter->setTrace($this->cat);
    $listener->runStarted();
    $errors= 0;
    $done= create('new util.collections.HashTable<xp.compiler.io.Source, xp.compiler.types.Types>()');
    foreach ($args as $arg) {
      try {
        create(new CompilationTask($arg, $listener, $manager, $emitter, $done))->run();
      } catch (CompilationException $e) {
        $errors++;
      }
    }
    $listener->runFinished();
    return 0 === $errors;
  }
  
  /**
   * Set log category for debugging
   *
   * @param   util.log.LogCategory cat
   */
  public function setTrace($cat) {
    $this->cat= $cat;
  }
}