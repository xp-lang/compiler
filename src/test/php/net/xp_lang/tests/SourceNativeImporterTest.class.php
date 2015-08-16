<?php namespace net\xp_lang\tests;

use xp\compiler\emit\php\NativeImporter;
use unittest\actions\VerifyThat;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.emit.php.NativeImporter
 */
#[@action(new VerifyThat(function() { return !defined('HHVM_VERSION'); }))]
class SourceNativeImporterTest extends \unittest\TestCase {
  protected $fixture= null;

  /** @return void */
  public function setUp() {
    $this->fixture= new NativeImporter();
  }

  /**
   * Test hasFunction()
   *
   */
  #[@test]
  public function hasFunction() {
    $this->assertTrue($this->fixture->hasFunction('standard', 'array_keys'), 'standard.array_keys');
    $this->assertTrue($this->fixture->hasFunction('pcre', 'preg_match'), 'pcre.preg_match');
    $this->assertTrue($this->fixture->hasFunction('core', 'strlen'), 'core.strlen');
  }
  
  /**
   * Test importing array_keys from ext/standard
   *
   */
  #[@test]
  public function importArray_keys() {
    $this->assertEquals(
      array('array_keys' => true), 
      $this->fixture->import('standard.array_keys')
    );
  }

  /**
   * Test importing preg_match from ext/pcre
   *
   */
  #[@test]
  public function importPreg_match() {
    $this->assertEquals(
      array('preg_match' => true), 
      $this->fixture->import('pcre.preg_match')
    );
  }

  /**
   * Test importing strlen from core
   *
   */
  #[@test]
  public function importStrlen() {
    $this->assertEquals(
      array('strlen' => true), 
      $this->fixture->import('core.strlen')
    );
  }

  /**
   * Test importing all functions from ext/standard
   *
   */
  #[@test]
  public function importAllFromStandard() {
    $this->assertEquals(
      array(0 => array('standard' => true)), 
      $this->fixture->import('standard.*')
    );
  }

  /**
   * Test importing from a nonexistant extension
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function importFromNonexistantExtension() {
   $this->fixture->import('nonexistant.extension');
  }

  /**
   * Test importing all functions from a nonexistant extension
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function importAllFromNonexistantExtension() {
   $this->fixture->import('nonexistant.*');
  }

  /**
   * Test importing a non-existant function in the standard extension
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function importNonexistantFunction() {
   $this->fixture->import('standard.nonexistant');
  }

  /**
   * Test importing a function from an incorrect extension
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function importFunctionFromIncorrectExtension() {
   $this->fixture->import('standard.preg_match');
  }

  /**
   * Test import() with '.*' as argument
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function importEverything() {
   $this->fixture->import('.');
  }

  /**
   * Test import() with '*' as argument
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function importStar() {
   $this->fixture->import('*');
  }

  /**
   * Test import() with an empty string
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function importEmpty() {
   $this->fixture->import('');
  }
}
