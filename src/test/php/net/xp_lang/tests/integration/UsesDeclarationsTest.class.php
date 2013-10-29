<?php namespace net\xp_lang\tests\integration;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\task\FileArgument;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\task\CompilationTask;
use xp\compiler\Syntax;
use io\streams\MemoryInputStream;
use io\streams\MemoryOutputStream;
use io\File;

/**
 * TestCase
 *
 */
class UsesDeclarationsTest extends \unittest\TestCase {
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
   * Sets up test case
   */
  public function setUp() {
    $this->counter= 0;
  }
  
  /**
   * Assertion helper
   *
   * @param   string[] uses
   * @return  string src
   * @throws  unittest.AssertionFailedError
   */
  protected function assertUses($uses, $src) {
    $src= sprintf($src, 'FixtureForUsesDeclarationsTest·'.($this->counter++));

    // Compile sourcecode
    $emitter= new V53Emitter();
    $scope= new TaskScope(new CompilationTask(
      new FileArgument(new File(__FILE__), self::$syntax),
      new NullDiagnosticListener(),
      new FileManager(),
      $emitter
    ));
    $emitter->emit(self::$syntax->parse(new MemoryInputStream($src), $this->name), $scope);

    // Verify assertion
    $this->assertEquals($uses, array_keys($scope->used));
  }
  
  /**
   * Test parent class is used
   */
  #[@test]
  public function emptyParentClassUsesObject() {
    $this->assertUses(
      array('lang.Object'),
      'public class %s { }'
    );
  }

  /**
   * Test parent class uses
   */
  #[@test]
  public function throwableSubclass() {
    $this->assertUses(
      array('lang.Throwable'),
      'public class %s extends Throwable { }'
    );
  }

  /**
   * Test parent class is used
   */
  #[@test]
  public function stringListExtension() {
    $this->assertUses(
      array('util.collections.IList'),
      'public interface %s<T> extends util.collections.IList<T> { }'
    );
  }

  /**
   * Test implemented interface is used
   */
  #[@test]
  public function runnableImplementation() {
    $this->assertUses(
      array('lang.Object', 'lang.Runnable'),
      'public class %s implements Runnable { }'
    );
  }

  /**
   * Test implemented interface is used
   */
  #[@test]
  public function stringListImplementation() {
    $this->assertUses(
      array('lang.Object', 'util.collections.IList'),
      'public class %s<T> implements util.collections.IList<T> { }'
    );
  }

  /**
   * Test member declaration
   */
  #[@test]
  public function memberTypeDeclarationGetsUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.Throwable'),
      'public class %s { 
        Throwable $member= null;
      }'
    );
  }

  /**
   * Test member declaration
   */
  #[@test]
  public function genericMemberTypeDeclarationGetsUsed() {
    $this->assertUses(
      array('lang.Object', 'util.collections.IList'),// TBD: 'lang.types.String',
      'public class %s { 
        util.collections.IList<lang.types.String> $list;
      }'
    );
  }

  /**
   * Test indexer declaration
   */
  #[@test]
  public function indexerTypeDeclarationGetsUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.types.String'),
      'public class %s { 
        lang.types.String this[int $index] {
          get { return $this.strings[$index]; }
          set { $this.strings[$index]= $value; }
        }
      }'
    );
  }


  /**
   * Test property declaration
   */
  #[@test]
  public function propertyTypeDeclarationGetsUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.types.String'),
      'public class %s { 
        lang.types.String current {
          get { return $this.strings[$this.offset]; }
          set { $this.strings[$this.offset]= $value; }
        }
      }'
    );
  }

  /**
   * Test member initialization
   */
  #[@test]
  public function memberInitializationToThrowableInstanceUsesThrowable() {
    $this->assertUses(
      array('lang.Object', 'lang.Throwable'),
      'public class %s { 
        var $member= new Throwable();
      }'
    );
  }

  /**
   * Test member initialization
   */
  #[@test]
  public function memberInitializationToThrowableArrayUsesThrowable() {
    $this->assertUses(
      array('lang.Object', 'lang.Throwable'),
      'public class %s { 
        var $member= new Throwable[] { } ;
      }'
    );
  }

  /**
   * Test member initialization
   */
  #[@test]
  public function memberInitializationToThrowableMapUsesThrowable() {
    $this->assertUses(
      array('lang.Object', 'lang.Throwable'),
      'public class %s { 
        var $member= new [:Throwable] {:} ;
      }'
    );
  }

  /**
   * Test member initialization
   */
  #[@test]
  public function memberInitializationToThrowableClassUsesThrowable() {
    $this->assertUses(
      array('lang.Object', 'lang.Throwable', 'lang.XPClass'),
      'public class %s { 
        var $member= Throwable::class;
      }'
    );
  }

  /**
   * Test member initialization: Anonymous class' parent class is not 
   * used (but added in emitTypeName())
   */
  #[@test]
  public function memberInitializationToAnonymousInstanceUsesRunnable() {
    $this->assertUses(
      array('lang.Object', 'lang.Runnable'),
      'public class %s { 
        var $member= new Runnable() {
          public void run() {
            // TBI
          }
        };
      }'
    );
  }

  /**
   * Test assignment
   */
  #[@test]
  public function localVariableAssginmentToThrowableInstanceUsesThrowable() {
    $this->assertUses(
      array('lang.Object', 'lang.Throwable'),
      'public class %s { 
        public static void main(string[] $args) {
          $instance= new Throwable();
        }
      }'
    );
  }

  /**
   * Test assignment: Anonymous class' parent class used.
   */
  #[@test]
  public function localVariableAssginmentToAnonymousInstanceUsesRunnable() {
    $this->assertUses(
      array('lang.Object', 'lang.Runnable'),
      'public class %s { 
        public static void main(string[] $args) {
          $instance= new Runnable() {
            public void run() {
              // TBI
            }
          };
        }
      }'
    );
  }

  /**
   * Test assignment
   */
  #[@test]
  public function localVariableAssginmentToThrowableClassUsesThrowable() {
    $this->assertUses(
      array('lang.Object', 'lang.Throwable'),
      'public class %s { 
        public static void main(string[] $args) {
          $class= lang.Throwable::class;
        }
      }'
    );
  }

  /**
   * Test method call
   */
  #[@test]
  public function staticCallToXpClassForNameUsesXpClass() {
    $this->assertUses(
      array('lang.Object', 'lang.XPClass'),
      'public class %s { 
        public static void main(string[] $args) {
          XPClass::forName($args[0]);
        }
      }'
    );
  }

  /**
   * Test method call
   */
  #[@test]
  public function methodCallsReturnValueDoesNotGetUsed() {
    $this->assertUses(
      array('lang.Object'),
      'public class %s { 
        public static void main(string[] $args) {
          self::class.getClassLoader();
        }
      }'
    );
  }

  /**
   * Test method declaration
   */
  #[@test]
  public function methodDeclarationsInterfaceReturnValueDoesNotGetUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.XPClass'),
      'public class %s { 
        static IClassLoader loaderOf(string $name) {
          return XPClass::forName($name).getClassLoader();
        }
        
        public static void main(string[] $args) {
          self::loaderOf($args[0]);
        }
      }'
    );
  }

  /**
   * Test method declaration
   */
  #[@test]
  public function methodDeclarationsClassReturnValueDoesNotGetUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.XPClass'),
      'public class %s { 
        static AbstractClassLoader loaderOf(string $name) {
          return XPClass::forName($name).getClassLoader();
        }

        public static void main(string[] $args) {
          self::loaderOf($args[0]);
        }
      }'
    );
  }

  /**
   * Test method declaration
   */
  #[@test]
  public function methodDeclarationsArrayReturnValueDoesNotGetUsed() {
    $this->assertUses(
      array('lang.Object'),
      'public class %s { 
        static AbstractClassLoader[] loadersOf(string $name) {
          // TBI
        }

        public static void main(string[] $args) {
          self::loaderOf($args[0]);
        }
      }'
    );
  }

  /**
   * Test method declaration
   */
  #[@test]
  public function methodDeclarationsMapReturnValueDoesNotGetUsed() {
    $this->assertUses(
      array('lang.Object'),
      'public class %s { 
        static [:AbstractClassLoader] loadersOf(string $name) {
          // TBI
        }

        public static void main(string[] $args) {
          self::loaderOf($args[0]);
        }
      }'
    );
  }

  /**
   * Test method declaration
   */
  #[@test]
  public function methodDeclarationsArgumentTypesGetUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.types.String'),
      'public class %s { 
        static void deleteFrom(lang.types.String $string, int? $pos, int? $length) {
          // TBI
        }
      }'
    );
  }

  /**
   * Test constructor declaration
   */
  #[@test]
  public function constructorDeclarationsArgumentTypesGetUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.types.String'),
      'public class %s { 
        public __construct(lang.types.String $string) {
          // TBI
        }
      }'
    );
  }

  /**
   * Test method declaration
   */
  #[@test]
  public function extensionMethodDeclarationsExtensionGetUsed() {
    $this->assertUses(
      array('lang.Object', 'lang.types.String'),
      'public class %s { 
        static void delete(this lang.types.String $self, int? $pos, int? $length) {
          // TBI
        }
      }'
    );
  }
}
