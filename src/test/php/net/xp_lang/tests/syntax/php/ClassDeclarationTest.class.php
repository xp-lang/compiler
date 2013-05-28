<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\ClassConstantNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class ClassDeclarationTest extends ParserTestCase {

  /**
   * Parse class source and return statements inside field declaration
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration;
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function emtpyClass() {
    $this->assertEquals(
      new ClassNode(
        0,                          // Modifiers
        null,                       // Annotations
        new TypeName('Empty'),      // Name
        null,                       // Parent
        array(),                    // Implements
        null                        // Body
      ), 
      $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Empty { } ?>')
    );
  }

  /**
   * Test class constant declaration
   *
   */
  #[@test]
  public function classConstant() {
    $this->assertEquals(array(new ClassConstantNode(
      'DEBUG',
      TypeName::$VAR,
      new IntegerNode('1')
    )), $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Logger { 
      const DEBUG = 1;
    } ?>')->body);
  }

  /**
   * Test class constant declaration
   *
   */
  #[@test]
  public function classConstants() {
    $this->assertEquals(array(
      new ClassConstantNode(
        'DEBUG',
        TypeName::$VAR,
        new IntegerNode('1')
      ), new ClassConstantNode(
        'WARN',
        TypeName::$VAR,
        new IntegerNode('2')
      )
    ), $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Logger { 
      const DEBUG = 1, WARN  = 2;
    } ?>')->body);
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function methodAndField() {
    $this->assertEquals(array(new FieldNode(array(
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => new TypeName('var'),
      'initialization'  => new NullNode()
    )), new MethodNode(array(
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations' => null,
      'name'        => 'getInstance',
      'returns'     => new TypeName('var'),
      'parameters'  => null, 
      'throws'      => null,
      'body'        => array(),
      'extension'   => null
    ))), $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Logger { 
      private static $instance= null;
      public static function getInstance() { /* ... */ }
    } ?>')->body);
  }
}
