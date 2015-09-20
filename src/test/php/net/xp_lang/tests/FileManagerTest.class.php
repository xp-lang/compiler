<?php namespace net\xp_lang\tests;

use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\types\TypeReference;
use io\File;
use io\Folder;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.io.FileManager
 */
class FileManagerTest extends \unittest\TestCase {
  private $fixture= null;
  private static $result;

  /**
   * Defines the xp.compiler.emit.EmitterResult used for testing
   *
   */
  #[@beforeClass]
  public static function defineResult() {
    self::$result= \lang\ClassLoader::defineClass('FileManagerTestEmitterResult', 'lang.Object', ['xp.compiler.emit.EmitterResult'], '{
      protected $type= null;
      public function __construct($name) { $this->type= new \xp\compiler\types\TypeReference(new \xp\compiler\types\TypeName($name)); }
      public function type() { return $this->type; }
      public function extension() { return ".test"; }
    }');
  }

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new FileManager();
  }
  
  /**
   * Creates a new type reference
   *
   * @param  string $name qualified name
   * @return xp.compiler.types.Types
   */
  private function newResultWithType($name) {
    return self::$result->newInstance($name);
  }
  
  /**
   * Assertion helper
   *
   * @param   string expected
   * @return  io.File target
   * @throws  unittest.AssertionFailedError
   */
  private function assertTarget($expected, File $target) {
    $this->assertEquals(
      (new File(strtr($expected, '/', DIRECTORY_SEPARATOR)))->getURI(),
      str_replace(rtrim(realpath('.'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR, '', $target->getURI())
    );
  }
  
  #[@test]
  public function target() {
    $this->assertTarget(
      'de/thekid/demo/Value.test',
      $this->fixture->getTarget($this->newResultWithType('de.thekid.demo.Value'))
    );
  }

  #[@test]
  public function targetWithOutputFolder() {
    $this->assertTarget(
      'build/de/thekid/demo/Value.test',
      $this->fixture->withOutput(new Folder('build'))->getTarget($this->newResultWithType('de.thekid.demo.Value'))
    );
  }

  #[@test]
  public function targetWithSource() {
    $source= new FileSource(new File('src/de/thekid/demo/Value.xp'));
    $this->assertTarget(
      'src/de/thekid/demo/Value.test',
      $this->fixture->getTarget($this->newResultWithType('de.thekid.demo.Value'), $source)
    );
  }

  #[@test]
  public function targetWithSourceWithoutPackage() {
    $source= new FileSource(new File('src/Value.xp'));
    $this->assertTarget(
      'src/Value.test',
      $this->fixture->getTarget($this->newResultWithType('de.thekid.demo.Value'), $source)
    );
  }

  #[@test]
  public function targetWithSourceMismatchingPackage() {
    $source= new FileSource(new File('src/com/thekid/demo/Value.xp'));
    $this->assertTarget(
      'src/com/thekid/demo/Value.test',
      $this->fixture->getTarget($this->newResultWithType('de.thekid.demo.Value'), $source)
    );
  }
}