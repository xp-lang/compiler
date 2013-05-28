<?php namespace xp\compiler\types;

/**
 * Represents the method scope
 *
 * @see     xp://xp.compiler.Scope
 */
class TaskScope extends Scope {
  
  /**
   * Constructor
   *
   * @param   xp.compiler.task.CompilationTask task
   */
  public function __construct(\xp\compiler\task\CompilationTask $task) {
    parent::__construct();
    $this->task= $task;
  }
}
