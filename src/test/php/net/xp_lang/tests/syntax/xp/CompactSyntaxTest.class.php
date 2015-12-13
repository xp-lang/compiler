<?php namespace net\xp_lang\tests\syntax\xp;

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
   * Parse method source and return method declaration
   *
   * @param   string $decl The method declaration
   * @return  xp.compiler.ast.MethodNode
   */
  protected function parse($decl) {
    return $this->parseTree('class Test { '.$decl.' }')->declaration->body[0];
  }

  /**
   * Test "-> (expr)" as shorthand for "{ return (expr); }"
   */
  #[@test]
  public function compact_return() {
    $this->assertEquals(new MethodNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'getName',
      'returns'    => new TypeName('string'),
      'parameters' => null,
      'throws'     => null,
      'body'       => [
        new ReturnNode(new MemberAccessNode(new VariableNode('this'), 'name'))
      ],
      'extension'  => null
    ]), $this->parse(
      'public string getName() -> $this.name;'
    ));
  }

  /**
   * Test compact assignment
   */
  #[@test]
  public function compact_assignment() {
    $this->assertEquals(new MethodNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'setName',
      'returns'    => TypeName::$VOID,
      'parameters' => [
        [
          'assign' => 'name',
        ]
      ],
      'throws'     => null,
      'body'       => [],
      'extension'  => null
    ]), $this->parse(
      'public void setName($this.name) { }'
    ));
  }

  /**
   * Test compact assignment
   */
  #[@test]
  public function compact_assignment_with_default() {
    $this->assertEquals(new ConstructorNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'parameters' => [
        [
          'assign'  => 'name',
          'default' => new NullNode()
        ]
      ],
      'throws'     => null,
      'body'       => []
    ]), $this->parse(
      'public __construct($this.name= null) { }'
    ));
  }

  /**
   * Test compact fluent interface
   */
  #[@test]
  public function compact_fluent_return_this() {
    $this->assertEquals(new MethodNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'withName',
      'returns'    => new TypeName('self'),
      'parameters' => [
        [
          'assign' => 'name',
        ]
      ],
      'throws'     => null,
      'body'       => [],
      'extension'  => null
    ]), $this->parse(
      'public this withName($this.name) { }'
    ));
  }
}
