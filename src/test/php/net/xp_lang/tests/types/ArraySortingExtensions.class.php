<?php namespace net\xp_lang\tests\types;

/**
 * Fixture for testing extension methods reflection
 *
 * @see   xp://xp.compiler.types.Types#getExtensions
 * @see   xp://net.xp_lang.tests.types.TypeReflectionTest
 */
class ArraySortingExtensions extends \lang\Object {
  
  static function __import($scope) {
    \xp::extensions(self::class, $scope);
  }

  /**
   * Returns a sorted array list
   *
   * @param   lang.types.ArrayList self
   * @return  lang.types.ArrayList
   */
  public static function sorted(\lang\types\ArrayList $self) {
    // Implementation here
  }
}
