<?php namespace net\xp_lang\tests\execution\source;

/**
 * Helper for lambda tests
 *
 * @see      xp://tests.execution.source.LambdaTest
 */
class Functions extends \lang\Object {
  
  /**
   * Apply a function to each element in an array
   *
   * @param   var[] in
   * @param   var func
   * @return  var[]
   */
  public static function apply($in, $func) {
    return array_map($func, $in);
  }

  /**
   * Filer each element in an array with a given function
   *
   * @param   var[] in
   * @param   var func
   * @return  var[]
   */
  public static function filter($in, $func) {
    return array_values(array_filter($in, $func));
  }
}
