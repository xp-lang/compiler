<?php namespace xp\compiler\emit;

/**
 * Result from emitting
 *
 */
interface EmitterResult {
  
  /**
   * Return type
   *
   * @return  xp.compiler.types.Types type
   */
  public function type();
}