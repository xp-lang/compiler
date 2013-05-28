<?php namespace xp\compiler\io;

/**
 * Represents a source object
 *
 * @see   xp://xp.compiler.io.FileManager
 */
interface Source {

  /**
   * Get input stream
   *
   * @return  io.streams.InputStream
   */
  public function getInputStream();
  
  /**
   * Get syntax
   *
   * @return  xp.compiler.Syntax
   */
  public function getSyntax();

  /**
   * Get URI of this sourcefile - as source in error messages and
   * warnings.
   *
   * @return  string
   */
  public function getURI();
}