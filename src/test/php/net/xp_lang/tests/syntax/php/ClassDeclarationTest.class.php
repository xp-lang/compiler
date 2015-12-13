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

class ClassDeclarationTest extends ParserTestCase {

  /**
   * Parse class source and return statements inside field declaration
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration;
  }

  #[@test]
  public function emtpyClass() {
    $this->assertEquals(
      new ClassNode(
        0,                          // Modifiers
        null,                       // Annotations
        new TypeName('Empty'),      // Name
        null,                       // Parent
        [],                    // Implements
        null                        // Body
      ), 
      $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Empty { } ?>')
    );
  }

  #[@test]
  public function classConstant() {
    $this->assertEquals([new ClassConstantNode(
      'DEBUG',
      TypeName::$VAR,
      new IntegerNode('1')
    )], $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Logger { 
      const DEBUG = 1;
    } ?>')->body);
  }

  #[@test]
  public function classConstants() {
    $this->assertEquals([
      new ClassConstantNode(
        'DEBUG',
        TypeName::$VAR,
        new IntegerNode('1')
      ), new ClassConstantNode(
        'WARN',
        TypeName::$VAR,
        new IntegerNode('2')
      )
    ], $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Logger { 
      const DEBUG = 1, WARN  = 2;
    } ?>')->body);
  }

  #[@test]
  public function methodAndField() {
    $this->assertEquals([new FieldNode([
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => new TypeName('var'),
      'initialization'  => new NullNode()
    ]), new MethodNode([
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations' => null,
      'name'        => 'getInstance',
      'returns'     => new TypeName('var'),
      'parameters'  => null, 
      'throws'      => null,
      'body'        => [],
      'extension'   => null
    ])], $this->parse('<?php namespace net\xp_lang\tests\syntax\php; class Logger { 
      private static $instance= null;
      public static function getInstance() { /* ... */ }
    } ?>')->body);
  }
}
