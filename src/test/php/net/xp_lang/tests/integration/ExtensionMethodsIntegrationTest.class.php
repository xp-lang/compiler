<?php namespace net\xp_lang\tests\integration;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\task\CompilationTask;
use xp\compiler\Syntax;
use io\Folder;
use io\IOException;
use lang\Runtime;
use lang\ClassLoader;
use lang\types\String;

/**
 * TestCase
 *
 * @see      xp://net.xp_lang.tests.integration.ExtensionMethodsIntegrationTestFixture
 */
class ExtensionMethodsIntegrationTest extends \unittest\TestCase {
  protected static $temp= null;
  
  /**
   * Compile sourcecode
   */
  #[@beforeClass]
  public static function compile() {
    self::$temp= new Folder(\lang\System::tempDir());
  
    // Compiler
    $emitter= new V53Emitter();
    $files= new FileManager();
    $files->setOutput(self::$temp);
    $task= new CompilationTask(
      new FileSource(ClassLoader::getDefault()->getResourceAsStream('net/xp_lang/tests/integration/src/StringExtensions.xp')),
      new NullDiagnosticListener(),
      $files,
      $emitter
    );
    $task->run();
  }

  /**
   * Run sourcecode in a new VM, using ExtensionMethodsIntegrationTestFixture.
   *
   * @param   string source
   * @return  var result
   * @throws  io.IOException
   */
  protected function run($source) {
    $p= Runtime::getInstance()->newInstance(
      Runtime::getInstance()->startupOptions()->withClassPath(self::$temp->getURI()),
      'class', 
      'net.xp_lang.tests.integration.ExtensionMethodsIntegrationTestFixture',
      array()
    );
    $p->in->write($source."\n");
    $p->in->close();
    $e= $p->err->read();
    $o= $p->out->read();
    $p->close();
    if ($e) {
      throw new IOException($e);
    }
    if ('+' === $o[0]) {
      return unserialize(substr($o, 1));
    } else if ('-' === $o[0]) {
      throw new \lang\IllegalStateException(substr($o, 1));
    }
    throw new IOException($o);
  }

  /**
   * Test trim() extension method
   */
  #[@test]
  public function trimMethod() {
    $this->assertEquals(
      new String('Hello'), 
      $this->run('return (new \lang\types\String(" Hello "))->trim(" ");')
    );
  }

  /**
   * Test non-existant extension method
   */
  #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/undefined method lang.types.String::nonExistant/')]
  public function nonExistantMethod() {
    $this->run('return (new \lang\types\String(" Hello "))->nonExistant();');
  }
}
