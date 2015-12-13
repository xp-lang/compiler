<?php namespace net\xp_lang\tests\integration;

use xp\compiler\emit\php\V54Emitter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
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
    $emitter= new V54Emitter();
    $scope= new TaskScope(new CompilationTask(
      new FileSource(new File(__FILE__), self::$syntax),
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
      ['lang.Object'],
      'public class %s { }'
    );
  }

  /**
   * Test parent class uses
   */
  #[@test]
  public function throwableSubclass() {
    $this->assertUses(
      ['lang.Throwable'],
      'public class %s extends Throwable { }'
    );
  }

  /**
   * Test parent class is used
   */
  #[@test]
  public function stringListExtension() {
    $this->assertUses(
      ['util.collections.IList'],
      'public interface %s<T> extends util.collections.IList<T> { }'
    );
  }

  /**
   * Test implemented interface is used
   */
  #[@test]
  public function runnableImplementation() {
    $this->assertUses(
      ['lang.Object', 'lang.Runnable'],
      'public class %s implements Runnable { }'
    );
  }

  /**
   * Test implemented interface is used
   */
  #[@test]
  public function stringListImplementation() {
    $this->assertUses(
      ['lang.Object', 'util.collections.IList'],
      'public class %s<T> implements util.collections.IList<T> { }'
    );
  }

  /**
   * Test member declaration
   */
  #[@test]
  public function memberTypeDeclarationGetsUsed() {
    $this->assertUses(
      ['lang.Object', 'lang.Throwable'],
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
      ['lang.Object', 'util.collections.IList'],// TBD: 'net.xp_lang.tests.StringBuffer',
      'public class %s { 
        util.collections.IList<net.xp_lang.tests.StringBuffer> $list;
      }'
    );
  }

  /**
   * Test indexer declaration
   */
  #[@test]
  public function indexerTypeDeclarationGetsUsed() {
    $this->assertUses(
      ['lang.Object', 'net.xp_lang.tests.StringBuffer'],
      'public class %s { 
        net.xp_lang.tests.StringBuffer this[int $index] {
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
      ['lang.Object', 'net.xp_lang.tests.StringBuffer'],
      'public class %s { 
        net.xp_lang.tests.StringBuffer current {
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
      ['lang.Object', 'lang.Throwable'],
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
      ['lang.Object', 'lang.Throwable'],
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
      ['lang.Object', 'lang.Throwable'],
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
      ['lang.Object', 'lang.Throwable', 'lang.XPClass'],
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
      ['lang.Object', 'lang.Runnable'],
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
      ['lang.Object', 'lang.Throwable'],
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
      ['lang.Object', 'lang.Runnable'],
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
      ['lang.Object', 'lang.Throwable'],
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
      ['lang.Object', 'lang.XPClass'],
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
      ['lang.Object'],
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
      ['lang.Object', 'lang.XPClass'],
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
      ['lang.Object', 'lang.XPClass'],
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
      ['lang.Object'],
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
      ['lang.Object'],
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
      ['lang.Object', 'net.xp_lang.tests.StringBuffer'],
      'public class %s { 
        static void deleteFrom(net.xp_lang.tests.StringBuffer $string, int? $pos, int? $length) {
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
      ['lang.Object', 'net.xp_lang.tests.StringBuffer'],
      'public class %s { 
        public __construct(net.xp_lang.tests.StringBuffer $string) {
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
      ['lang.Object', 'net.xp_lang.tests.StringBuffer'],
      'public class %s { 
        static void delete(this net.xp_lang.tests.StringBuffer $self, int? $pos, int? $length) {
          // TBI
        }
      }'
    );
  }
}
