<?php namespace net\xp_lang\tests\execution\source;

/**
 * Generic filter
 *
 * @see      xp://net.xp_lang.tests.execution.source.InstanceCreationTest
 */
#[@generic(self= 'T')]
interface Filter {
  
  /**
   * Returns whether this element should be accepted
   *
   * @param   T element
   * @return  bool
   */
  #[@generic(params= 'T')]
  public function accept($element);
}