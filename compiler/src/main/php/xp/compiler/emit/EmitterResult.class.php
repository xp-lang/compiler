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

  /**
   * Return file extension including the leading dot
   *
   * @return  string
   */
  public function extension();
}