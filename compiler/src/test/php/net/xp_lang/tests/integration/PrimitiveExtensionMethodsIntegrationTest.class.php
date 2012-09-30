<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.System',
    'lang.types.String',
    'io.Folder',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.io.StringSource',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   */
  class PrimitiveExtensionMethodsIntegrationTest extends TestCase {
    protected static $syntax;
    protected $counter= 0;

    /**
     * Sets up compiler API
     *
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
    
      $emitter= new xp·compiler·emit·source·Emitter();
      $task= new CompilationTask(
        new StringSource(sprintf($decl, $this->counter++, $source), self::$syntax, $this->name),
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
     *
     */
    #[@test]
    public function trimMethod() {
      $this->assertEquals(
        'hello world', 
        $this->compile('return ["hello", "world"].join(" ");')->run()
      );
    }
  }
?>
