<?php namespace net\xp_lang\tests;

use xp\compiler\io\StringSource;
use io\streams\Streams;

class StringSourceTest extends \unittest\TestCase {
  protected static $syntax;

  /**
   * Use XP language
   *
   * @return void
   */
  #[@beforeClass]
  public static function useXpSyntax() {
    self::$syntax= Syntax::forName('xp');
  }
  
  /**
   * Creates a new fixture
   *
   * @param   string source
   * @return  xp.compiler.io.StringSource
   */
  protected function newInstance($source= null) {
    return new StringSource($source, self::$syntax, $this->name);
  }

  #[@test]
  public function getInputStream() {
    $source= 'Console::writeLine("Hello");';
    $this->assertEquals($source, Streams::readAll($this->newInstance($source)->getInputStream()));
  }

  #[@test]
  public function getSyntax() {
    $this->assertEquals(self::$syntax, $this->newInstance()->getSyntax());
  }

  #[@test]
  public function getURI() {
    $this->assertEquals($this->name, $this->newInstance()->getURI());
  }

  #[@test]
  public function getURIWithNameOmitted() {
    $this->assertEquals(
      'Compiled source #0', 
      (new StringSource(null, self::$syntax))->getURI()
    );
  }
}