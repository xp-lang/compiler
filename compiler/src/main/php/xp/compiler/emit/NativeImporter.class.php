<?php namespace xp\compiler\emit;

/**
 * Imports native functions. Implementations are constructed inside the 
 * emitter implementations.
 *
 * @see   xp://xp.compiler.emit.source.NativeImporter
 */
abstract class NativeImporter extends \lang\Object {
  
  /**
   * Import all functions from a given extension
   *
   * @param   string extension
   * @param   string function
   * @return  array<var, var> import
   * @throws  lang.IllegalArgumentException if extension or function don't exist
   */
  public abstract function importAll($extension);
  
  /**
   * Import a single function
   *
   * @param   string extension
   * @param   string function
   * @return  array<var, var> import
   * @throws  lang.IllegalArgumentException if extension or function don't exist
   */
  public abstract function importSelected($extension, $function);
  
  /**
   * Check whether a given function exists
   *
   * @param   string extension
   * @param   string function
   * @return  array<var, var> import
   * @throws  lang.IllegalArgumentException if extension or function don't exist
   */
  public abstract function hasFunction($extension, $function);
  
  /**
   * Import a given pattern
   *
   * Specific:
   * ```php
   * import native standard.array_keys;
   * import native pcre.preg_match;
   * import native core.strlen;
   * ```
   *
   * On-Demand:
   * ```php
   * import native standard.*;
   * ```
   *
   * @param   string pattern
   * @return  [:var] import
   * @throws  lang.IllegalArgumentException in case errors occur during importing
   * @throws  lang.FormatException in case the input string is malformed
   */
  public function import($pattern) {
    $p= strrpos($pattern, '.');
    if ($p <= 0) {
      throw new \lang\FormatException('Malformed import <'.$pattern.'>');
    } else  if ('.*' == substr($pattern, -2)) {
      return $this->importAll(substr($pattern, 0, $p));
    } else {
      return $this->importSelected(substr($pattern, 0, $p), substr($pattern, $p+ 1));
    }
  }
}
