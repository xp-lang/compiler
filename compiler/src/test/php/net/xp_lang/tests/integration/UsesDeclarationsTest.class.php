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
     * Test parent class is not used (but added in emitTypeName())
     *
     */
    #[@test]
    public function emptyClass() {
      $this->assertEquals(
        array(), 
        $this->usedClassesIn('public class %s { }')
      );
    }

    /**
     * Test parent class is not used (but added in emitTypeName())
     *
     */
    #[@test]
    public function throwableSubclass() {
      $this->assertEquals(
        array(), 
        $this->usedClassesIn('public class %s extends Throwable { }')
      );
    }

    /**
     * Test implemnted interface is not used (but added in emitTypeName())
     *
     */
    #[@test]
    public function runnableImplementation() {
      $this->assertEquals(
        array(), 
        $this->usedClassesIn('public class %s implements Runnable { }')
      );
    }

    /**
     * Test member declaration
     *
     */
    #[@test]
    public function memberTypeDeclarationGetsUsed() {
      $this->assertEquals(
        array(new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s { 
          Throwable $member= null;
        }')
      );
    }

    /**
     * Test member declaration
     *
     */
    #[@test]
    public function genericMemberTypeDeclarationGetsUsed() {
      $this->assertEquals(
        array(new TypeName('util.collections.IList')), // TBD: new TypeName('lang.types.String')), 
        $this->usedClassesIn('public class %s { 
          util.collections.IList<lang.types.String> $list;
        }')
      );
    }

    /**
     * Test indexer declaration
     *
     */
    #[@test]
    public function indexerTypeDeclarationGetsUsed() {
      $this->assertEquals(
        array(new TypeName('lang.types.String')), 
        $this->usedClassesIn('public class %s { 
          lang.types.String this[int $index] {
            get { return $this.strings[$index]; }
            set { $this.strings[$index]= $value; }
          }
        }')
      );
    }


    /**
     * Test property declaration
     *
     */
    #[@test]
    public function propertyTypeDeclarationGetsUsed() {
      $this->assertEquals(
        array(new TypeName('lang.types.String')), 
        $this->usedClassesIn('public class %s { 
          lang.types.String current {
            get { return $this.strings[$this.offset]; }
            set { $this.strings[$this.offset]= $value; }
          }
        }')
      );
    }

    /**
     * Test member initialization
     *
     */
    #[@test]
    public function memberInitializationToThrowableInstanceUsesThrowable() {
      $this->assertEquals(
        array(new TypeName('lang.Throwable')), 
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
        array(new TypeName('lang.Throwable'), new TypeName('lang.XPClass')), 
        $this->usedClassesIn('public class %s { 
          var $member= Throwable::class;
        }')
      );
    }

    /**
     * Test member initialization: Anonymous class' parent class is not 
     * used (but added in emitTypeName())
     *
     */
    #[@test]
    public function memberInitializationToAnonymousInstanceUsesRunnable() {
      $this->assertEquals(
        array(new TypeName('lang.Runnable')), 
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
        array(new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            $instance= new Throwable();
          }
        }')
      );
    }

    /**
     * Test assignment: Anonymous class' parent class is not used (but 
     * added in emitTypeName())
     *
     */
    #[@test]
    public function localVariableAssginmentToAnonymousInstanceUsesRunnable() {
      $this->assertEquals(
        array(), 
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
        array(new TypeName('lang.Throwable')), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            $class= lang.Throwable::class;
          }
        }')
      );
    }

    /**
     * Test method call
     *
     */
    #[@test]
    public function staticCallToXpClassForNameUsesXpClass() {
      $this->assertEquals(
        array(new TypeName('lang.XPClass')), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            XPClass::forName($args[0]);
          }
        }')
      );
    }

    /**
     * Test method call
     *
     */
    #[@test]
    public function methodCallsReturnValueDoesNotGetUsed() {
      $this->assertEquals(
        array(), 
        $this->usedClassesIn('public class %s {
          public static void main(string[] $args) {
            self::class.getClassLoader();
          }
        }')
      );
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function methodDeclarationsReturnValueDoesNotGetUsed() {
      $this->assertEquals(
        array(new TypeName('lang.XPClass')), 
        $this->usedClassesIn('public class %s {
          static IClassLoader loaderOf(string $name) {
            return XPClass::forName($name).getClassLoader();
          }
          
          public static void main(string[] $args) {
            self::loaderOf($args[0]);
          }
        }')
      );
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function methodDeclarationsArgumentTypesGetUsed() {
      $this->assertEquals(
        array(new TypeName('lang.types.String')), 
        $this->usedClassesIn('public class %s {
          static void deleteFrom(lang.types.String $string, int? $pos, int? $length) {
            // TBI
          }
        }')
      );
    }

    /**
     * Test constructor declaration
     *
     */
    #[@test]
    public function constructorDeclarationsArgumentTypesGetUsed() {
      $this->assertEquals(
        array(new TypeName('lang.types.String')), 
        $this->usedClassesIn('public class %s {
          public __construct(lang.types.String $string) {
            // TBI
          }
        }')
      );
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function extensionMethodDeclarationsExtensionGetUsed() {
      $this->assertEquals(
        array(new TypeName('lang.types.String')), 
        $this->usedClassesIn('public class %s {
          static void delete(this lang.types.String $self, int? $pos, int? $length) {
            // TBI
          }
        }')
      );
    }
  }
?>
