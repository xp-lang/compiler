<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

 $package= 'net.xp_lang.tests.types';

  uses('lang.types.ArrayList');

  /**
   * Fixture for testing extension methods reflection
   *
   * @see   xp://xp.compiler.types.Types#getExtensions
   * @see   xp://net.xp_lang.tests.types.TypeReflectionTest
   */
  class net·xp_lang·tests·types·ArraySortingExtensions extends Object {
    static function __import($scope) {
      xp::extensions(__CLASS__, $scope);
    }

    /**
     * Returns a sorted array list
     *
     * @param   lang.types.ArrayList self
     * @return  lang.types.ArrayList
     */
    public static function sorted(ArrayList $self) {
      // Implementation here
    }
  }
?>
