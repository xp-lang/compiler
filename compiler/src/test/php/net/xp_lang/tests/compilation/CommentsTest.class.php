<?php namespace net\xp_lang\tests\compilation;

use xp\compiler\emit\source\Emitter;
use xp\compiler\types\TypeName;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\task\CompilationTask;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\Syntax;
use io\File;
use io\streams\MemoryInputStream;
use lang\XPClass;

/**
 * TestCase
 *
 */
class CommentsTest extends \unittest\TestCase {
  protected $scope;
  protected $emitter;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->emitter= new Emitter();
    $this->scope= new TaskScope(new CompilationTask(
      new FileSource(new File(__FILE__), Syntax::forName('xp')),
      new NullDiagnosticListener(),
      new FileManager(),
      $this->emitter
    ));
  }

  /**
   * Compile class from source and return compiled type
   *
   * @param   string src
   * @return  xp.compiler.types.Types
   */
  protected function compile($src) {
    $r= $this->emitter->emit(
      Syntax::forName('xp')->parse(new MemoryInputStream($src)),
      $this->scope
    );
    $r->executeWith(array());
    return XPClass::forName($r->type()->name());
  }

  /**
   * Test XPClass::getComment() on compiled type
   *
   */
  #[@test]
  public function classWithoutComment() {
    $this->assertNull($this->compile('class ClassWithoutComment { }')->getComment());
  }

  /**
   * Test XPClass::getComment() on compiled type
   *
   */
  #[@test]
  public function classWithComment() {
    $class= $this->compile('
      /**
       * Person class
       */
      class ClassWithComment { }'
    );
    $this->assertEquals('Person class', $class->getComment());
  }

  /**
   * Test XPClass::getComment() on compiled type
   *
   */
  #[@test]
  public function classWithMultilineComment() {
    $class= $this->compile('
      /**
       * Defines a class with a long comment
       * spanning multiple lines.
       */
      class ClassWithMultilineComment { }'
    );
    $this->assertEquals("Defines a class with a long comment\nspanning multiple lines.", $class->getComment());
  }

  /**
   * Test XPClass::getComment() on compiled type
   *
   */
  #[@test]
  public function classWithCommentWithDocTags() {
    $class= $this->compile('
      /**
       * Person class
       *
       * @see   xp://net.xp_lang.tests.compilation.CommentsTest
       */
      class ClassWithCommentWithDocTags { }'
    );
    $this->assertEquals('Person class', $class->getComment());
  }

  /**
   * Test Method::getComment() on compiled type
   *
   */
  #[@test]
  public function methodWithoutComment() {
    $class= $this->compile('
      class MethodWithoutComment { 
        public static void main(string[] $args) { }
      }'
    );
    $this->assertNull($class->getMethod('main')->getComment());
  }

  /**
   * Test Method::getComment() on compiled type
   *
   */
  #[@test]
  public function methodWithComment() {
    $class= $this->compile('
      class MethodWithComment {

        /**
         * Entry point method
         */
        public static void main(string[] $args) { }
      }'
    );
    $this->assertEquals('Entry point method', $class->getMethod('main')->getComment());
  }

  /**
   * Test Method::getComment() on compiled type
   *
   */
  #[@test]
  public function methodWithMultilineComment() {
    $class= $this->compile('
      class MethodWithMultilineComment {

        /**
         * Entry point method.
         *
         * Called when run from the command line.
         */
        public static void main(string[] $args) { }
      }'
    );
    $this->assertEquals("Entry point method.\n\nCalled when run from the command line.", $class->getMethod('main')->getComment());
  }

  /**
   * Test Method::getComment() on compiled type
   *
   */
  #[@test]
  public function methodWithCommentWithDocTags() {
    $class= $this->compile('
      class MethodWithCommentWithDocTags {

        /**
         * Entry point method
         *
         * @see   xp://net.xp_lang.tests.compilation.CommentsTest
         */
        public static void main(string[] $args) { }
      }'
    );
    $this->assertEquals('Entry point method', $class->getMethod('main')->getComment());
  }
}
