<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   */
  class UsesDeclarationsTest extends TestCase {
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
     * Sets up test case
     *
     */
    public function setUp() {
      $this->counter= 0;
    }
    
    /**
     * Returns scope after compiling
     *
     * @param   string src
     * @return  xp.compiler.types.Scope
     */
    protected function usedClassesIn($src) {
      $emitter= new xp·compiler·emit·source·Emitter();
      $scope= new TaskScope(new CompilationTask(
        new FileSource(new File(__FILE__), self::$syntax),
        new NullDiagnosticListener(),
        new FileManager(),
        $emitter
      ));
      $emitter->emit(
        self::$syntax->parse(new MemoryInputStream(sprintf($src, 'FixtureForUsesDeclarationsTest·'.($this->counter++))), $this->name),
        $scope
      );
      return $scope->used;
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function emptyClassUsesObject() {
      $this->assertEquals(
        array(new TypeName('lang.Object')), 
        $this->usedClassesIn('public class %s { }')
      );
    }

    /**
     * Test extends
     *
     */
    #[@test]
    public function throwableSubclassUsesThrowable() {
      $this->assertEquals(
        array(new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s extends Throwable { }')
      );
    }

    /**
     * Test implements
     *
     */
    #[@test]
    public function runnableImplementationUsesObjectAndRunnable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Runnable')), 
        $this->usedClassesIn('public class %s implements Runnable { }')
      );
    }

    /**
     * Test member initialization
     *
     */
    #[@test]
    public function memberInitializationToThrowableInstanceUsesThrowable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s { 
          var $member= new Throwable();
        }')
      );
    }

    /**
     * Test member initialization
     *
     */
    #[@test]
    public function memberInitializationToThrowableClassUsesThrowable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s { 
          var $member= Throwable::class;
        }')
      );
    }

    /**
     * Test member initialization
     *
     */
    #[@test]
    public function memberInitializationToAnonymousInstanceUsesRunnable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Runnable')), 
        $this->usedClassesIn('public class %s { 
          var $member= new Runnable() {
            public void run() {
              // TBI
            }
          };
        }')
      );
    }

    /**
     * Test assignment
     *
     */
    #[@test]
    public function localVariableAssginmentToThrowableInstanceUsesThrowable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            $instance= new Throwable();
          }
        }')
      );
    }

    /**
     * Test assignment
     *
     */
    #[@test]
    public function localVariableAssginmentToAnonymousInstanceUsesRunnable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Runnable')), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            $instance= new Runnable() {
              public void run() {
                // TBI
              }
            };
          }
        }')
      );
    }

    /**
     * Test assignment
     *
     */
    #[@test]
    public function localVariableAssginmentToThrowableClassUsesThrowable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            $class= lang.Throwable::class;
          }
        }')
      );
    }

    /**
     * Test assignment
     *
     */
    #[@test]
    public function localVariableAssginmentToThrowableClassLoaderUsesThrowable() {
      $this->assertEquals(
        array(new TypeName('lang.Object'), new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            $loader= lang.Throwable::class.getClassLoader();
          }
        }')
      );
    }
  }
?>
