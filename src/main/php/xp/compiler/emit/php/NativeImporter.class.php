<?php namespace xp\compiler\emit\php;

use lang\IllegalArgumentException;

/**
 * Imports native functions from PHP.
 *
 * @test    xp://net.xp_lang.tests.SourceNativeImporterTest
 */
class NativeImporter extends \xp\compiler\emit\NativeImporter {
  
  /**
   * Import all functions from a given extension
   *
   * @param   string extension
   * @param   string function
   * @return  [:bool] import
   * @throws  lang.IllegalArgumentException if extension or function don't exist
   */
  public function importAll($extension) {
    try {
      $e= new \ReflectionExtension($extension);
    } catch (\ReflectionException $e) {
      throw new IllegalArgumentException('Extension '.$extension.' does not exist');
    }
    return [0 => [$extension => true]];
  }
  
  /**
   * Import a single function
   *
   * @param   string extension
   * @param   string function
   * @return  [:bool] import
   * @throws  lang.IllegalArgumentException if extension or function don't exist
   */
  public function importSelected($extension, $function) {
    try {
      $f= new \ReflectionFunction($function);
    } catch (\ReflectionException $e) {
      throw new IllegalArgumentException($e->getMessage());
    }

    if ($extension === strtolower(($fe= $f->getExtensionName()))) {
      return [$function => true];
    } else {
      throw new IllegalArgumentException('Function '.$function.' is not inside extension '.$extension.' (but '.($fe ?: '(n/a)').')');
    }
  }

  /**
   * Check whether a given function exists
   *
   * @param   string extension
   * @param   string function
   * @return  bool
   * @throws  lang.IllegalArgumentException if extension or function don't exist
   */
  public function hasFunction($extension, $function) {
    if (function_exists($function)) {
      $e= (new \ReflectionFunction($function))->getExtensionName();
      return $extension === strtolower($e);
    } else {
      return false;
    }
  }
  
  /**
   * Import a given pattern
   *
   * Specific:
   * <code>
   *   import native standard.array_keys;
   *   import native pcre.preg_match;
   *   import native core.strlen;
   * </code>
   *
   * On-Demand:
   * <code>
   *   import native standard.*;
   * </code>
   *
   * @param   string pattern
   * @return  [:bool] import
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
