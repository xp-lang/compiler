<?php namespace xp\compiler\task;

/**
 * Source implementation based on files
 */
interface Argument {
  
  /**
   * Get sources
   *
   * @return  xp.compiler.io.Source[]
   */
  public function getSources();
}