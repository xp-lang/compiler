<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

 $package= 'net.xp_lang.tests.types';

  /**
   * Fixture for testing extension methods reflection
   *
   * @see   xp://net.xp_lang.tests.types.TypeReflectionTest
   */
  class net·xp_lang·tests·types·Builder extends Object {

    /**
     * Creates a new builder instance by copying an existing one if given
     *
     * @param   self self
     * @return  self
     */
    public static function create(self $self= NULL) {
      $copy= clone $self;
      // TBI
      return $copy;
    }
  }
?>
