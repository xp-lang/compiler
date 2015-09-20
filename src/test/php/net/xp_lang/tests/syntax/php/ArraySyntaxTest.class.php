<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\VariableNode;

class ArraySyntaxTest extends ParserTestCase {

  /**
   * Assertion helper
   *
   * @param  xp.compiler.ast.Node $offset
   * @param  string $syntax
   * @throws unittest.AssertionFailedError
   */
  private function assertArrayAccess($offset, $syntax) {
    $this->assertEquals(
      [new ArrayAccessNode(new VariableNode('fixture'), $offset)],
      $this->parse($syntax)
    );
  }

  #[@test]
  public function integer_offset() {
    $this->assertArrayAccess(new IntegerNode('1'), '$fixture[1];');
  }

  #[@test]
  public function string_offset() {
    $this->assertArrayAccess(new StringNode('a'), '$fixture["a"];');
  }

  #[@test]
  public function variable_offset() {
    $this->assertArrayAccess(new VariableNode('i'), '$fixture[$i];');
  }

  #[@test]
  public function no_offset() {
    $this->assertArrayAccess(null, '$fixture[];');
  }

  #[@test]
  public function curly_braces() {
    $this->assertArrayAccess(new VariableNode('i'), '$fixture{$i};');
  }
}
