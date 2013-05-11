<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\ConstructorNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\NullNode;
use xp\compiler\types\TypeName;

/**
 * TestCase for compact syntax
 *
 * @see   https://github.com/xp-framework/rfc/issues/240
 * @see   https://github.com/xp-framework/rfc/issues/241
 * @see   https://github.com/xp-framework/rfc/issues/252
 */
class CompactSyntaxTest extends ParserTestCase {

  /**
   * Parse method source and return statements inside this method.
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
  }

  /**
   * Test "-> (expr)" as shorthand for "{ return (expr); }"
   */
  #[@test]
  public function compact_return() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'getName',
      'returns'    => new TypeName('string'),
      'parameters' => null,
      'throws'     => null,
      'body'       => array(
        new ReturnNode(new MemberAccessNode(new VariableNode('this'), 'name'))
      ),
      'extension'  => null
    ))), $this->parse('class Null { 
      public string getName() -> $this.name;
    }'));
  }

  /**
   * Test compact assignment
   */
  #[@test]
  public function compact_assignment() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'setName',
      'returns'    => TypeName::$VOID,
      'parameters' => array(
        array(
          'assign' => 'name',
        )
      ),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Null { 
      public void setName($this.name) { }
    }'));
  }

  /**
   * Test compact assignment
   */
  #[@test]
  public function compact_assignment_with_default() {
    $this->assertEquals(array(new ConstructorNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'parameters' => array(
        array(
          'assign'  => 'name',
          'default' => new NullNode()
        )
      ),
      'throws'     => null,
      'body'       => array()
    ))), $this->parse('class Null { 
      public __construct($this.name= null) { }
    }'));
  }

  /**
   * Test compact fluent interface
   */
  #[@test]
  public function compact_fluent_return_this() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'withName',
      'returns'    => new TypeName('self'),
      'parameters' => array(
        array(
          'assign' => 'name',
        )
      ),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Null { 
      public this withName($this.name) { }
    }'));
  }
}
