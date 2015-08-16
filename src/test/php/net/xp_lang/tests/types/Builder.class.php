<?php namespace net\xp_lang\tests\types;

/**
 * Fixture for testing extension methods reflection
 *
 * @see   xp://net.xp_lang.tests.types.TypeReflectionTest
 */
class Builder extends \lang\Object {

  /**
   * Creates a new builder instance by copying an existing one if given
   *
   * @param   self self
   * @return  self
   */
  public static function (self $self= null) {
    $copy= clone $self;
    // TBI
    return $copy;
  }
}