<?php namespace net\xp_lang\tests\integration;

use util\cmd\Console;
new \import('StringExtensions');

/**
 * Test fixture
 */
class ExtensionMethodsIntegrationTestFixture extends \lang\Object {
  
  /**
   * Entry point method
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    try {
      Console::$out->writeLine('+', serialize(eval(Console::$in->readLine())));
    } catch (\lang\Throwable $e) {
      Console::$out->writeLine('-', $e->getMessage());
    }
  }
}
