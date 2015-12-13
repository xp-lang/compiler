<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\NullNode;
use xp\compiler\types\TypeName;

class FieldDeclarationTest extends ParserTestCase {

  /**
   * Parse class source and return statements inside field declaration
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer('<?php '.$src.' ?>', '<string:'.$this->name.'>'))->declaration->body;
  }

  #[@test]
  public function publicField() {
    $this->assertEquals([new FieldNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'name',
      'type'       => TypeName::$VAR,
      'initialization' => null,
    ])], $this->parse('class Person { 
      public $name;
    }'));
  }

  #[@test]
  public function privateStaticField() {
    $this->assertEquals([new FieldNode([
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => TypeName::$VAR,
      'initialization'  => new NullNode()
    ])], $this->parse('class Logger { 
      private static $instance= null;
    }'));
  }
}
