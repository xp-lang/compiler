<?php namespace net\xp_lang\tests\integration;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\task\CompilationTask;
use xp\compiler\task\StringArgument;
use xp\compiler\Syntax;

/**
 * TestCase
 *
 */
class PrimitiveExtensionMethodsIntegrationTest extends \unittest\TestCase {
  protected static $syntax;
  protected $counter= 0;

  /**
   * Sets up compiler API
   */
  #[@beforeClass]
  public static function useXpSyntax() {
    self::$syntax= Syntax::forName('xp');
  }

  /**
   * Compile and then run sourcecode
   *
   * @param   string source
   * @return  lang.Runnable
   */
  protected function compile($source) {
    $decl= '
    import integrationtests.ArrayExtensions;
    
    class FixturePrimitiveExtensionMethodsIntegrationTest·%d implements Runnable {
      public var run() {
        %s
      }
    }';
  
    $emitter= new V53Emitter();
    $task= new CompilationTask(
      new StringArgument(sprintf($decl, $this->counter++, $source), self::$syntax, $this->name)),
      new NullDiagnosticListener(),
      newinstance('xp.compiler.io.FileManager', array($this->getClass()->getClassLoader()), '{
        protected $cl;
        
        public function __construct($cl) {
          $this->cl= $cl;
        }
        
        public function findClass($qualified) {
          return new FileSource($this->cl->getResourceAsStream("net/xp_lang/tests/integration/src/".strtr($qualified, ".", "/").".xp"));
        }
        
        public function write($r, File $target) {
          // DEBUG $r->writeTo(Console::$out->getStream());
          $r->executeWith(array());   // Defines the class
        }
      }'),
      $emitter
    );
    $type= $task->run();
    return XPClass::forName($type->name())->newInstance();
  }

  /**
   * Test trim() extension method
   */
  #[@test]
  public function trimMethod() {
    $this->assertEquals(
      'hello world', 
      $this->compile('return ["hello", "world"].join(" ");')->run()
    );
  }
}
