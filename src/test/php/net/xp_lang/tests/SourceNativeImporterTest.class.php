<?php namespace net\xp_lang\tests;

use xp\compiler\emit\php\NativeImporter;
use unittest\actions\VerifyThat;
use lang\IllegalArgumentException;
use lang\FormatException;

#[@action(new VerifyThat(function() { return !defined('HHVM_VERSION'); }))]
class SourceNativeImporterTest extends \unittest\TestCase {
  protected $fixture= null;

  /** @return void */
  public function setUp() {
    $this->fixture= new NativeImporter();
  }

  #[@test]
  public function hasFunction() {
    $this->assertTrue($this->fixture->hasFunction('standard', 'array_keys'), 'standard.array_keys');
    $this->assertTrue($this->fixture->hasFunction('pcre', 'preg_match'), 'pcre.preg_match');
    $this->assertTrue($this->fixture->hasFunction('core', 'strlen'), 'core.strlen');
  }
  
  #[@test]
  public function importArray_keys() {
    $this->assertEquals(
      array('array_keys' => true), 
      $this->fixture->import('standard.array_keys')
    );
  }

  #[@test]
  public function importPreg_match() {
    $this->assertEquals(
      array('preg_match' => true), 
      $this->fixture->import('pcre.preg_match')
    );
  }

  #[@test]
  public function importStrlen() {
    $this->assertEquals(
      array('strlen' => true), 
      $this->fixture->import('core.strlen')
    );
  }

  #[@test]
  public function importAllFromStandard() {
    $this->assertEquals(
      array(0 => array('standard' => true)), 
      $this->fixture->import('standard.*')
    );
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function importFromNonexistantExtension() {
   $this->fixture->import('nonexistant.extension');
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function importAllFromNonexistantExtension() {
   $this->fixture->import('nonexistant.*');
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function importNonexistantFunction() {
   $this->fixture->import('standard.nonexistant');
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function importFunctionFromIncorrectExtension() {
   $this->fixture->import('standard.preg_match');
  }

  #[@test, @expect(FormatException::class)]
  public function importEverything() {
   $this->fixture->import('.');
  }

  #[@test, @expect(FormatException::class)]
  public function importStar() {
   $this->fixture->import('*');
  }

  #[@test, @expect(FormatException::class)]
  public function importEmpty() {
   $this->fixture->import('');
  }
}
