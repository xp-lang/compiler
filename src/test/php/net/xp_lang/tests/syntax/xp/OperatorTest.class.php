<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\ast\OperatorNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class OperatorTest extends ParserTestCase {

  /**
   * Parse operator source and return statements inside this operator.
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
  }

  #[@test]
  public function concat_operator() {
    $this->assertEquals(array(new OperatorNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'name'       => '',
      'symbol'     => '~',
      'returns'    => new TypeName('self'),
      'parameters' => array(
        array('name' => 'self', 'type' => new TypeName('self'), 'check' => true),
        array('name' => 'arg', 'type' => TypeName::$VAR, 'check' => true),
      ),
      'throws'     => null,
      'body'       => array()
    ))), $this->parse('class String { 
      public static self operator ~(self $self, var $arg) { }
    }'));
  }
}
