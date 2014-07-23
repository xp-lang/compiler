<?php namespace net\xp_lang\tests\integration;

#[@generic(self= 'R')]
class Sequence extends \lang\Object {
  
  /**
   * Factory
   *
   * @param  var $input
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public static function of($input) {
    // TBI
  }
}