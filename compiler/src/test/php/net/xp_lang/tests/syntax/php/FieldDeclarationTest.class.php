<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\NullNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class FieldDeclarationTest extends ParserTestCase {

  /**
   * Parse class source and return statements inside field declaration
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return create(new Parser())->parse(new Lexer('<?php '.$src.' ?>', '<string:'.$this->name.'>'))->declaration->body;
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function publicField() {
    $this->assertEquals(array(new FieldNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'name',
      'type'       => TypeName::$VAR,
      'initialization' => null,
    ))), $this->parse('class Person { 
      public $name;
    }'));
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function privateStaticField() {
    $this->assertEquals(array(new FieldNode(array(
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => TypeName::$VAR,
      'initialization'  => new NullNode()
    ))), $this->parse('class Logger { 
      private static $instance= null;
    }'));
  }
}
