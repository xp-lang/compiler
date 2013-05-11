<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\IsInlineable;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IfNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.IsInlineable
 */
class IsInlineableTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new IsInlineable();
  }
  
  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.MethodNode declaration
   * @return  var
   */
  protected function verify(MethodNode $declaration) {
    return $this->fixture->verify($declaration, new TypeDeclarationScope());
  }
  
  /**
   * Tests the following is inlineable:
   * <code>
   *   inline T inc(T $in) { return ++$in; }
   * </code>
   */
  #[@test]
  public function oneLineMethodIsInlineable() {
    $this->assertNull(
      $this->verify(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'in')),
        'body'        => array(
          new ReturnNode(
            new UnaryOpNode(array('op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')))
          )
        )
      )))
    );
  }

  /**
   * Tests the following is not inlineable:
   * <code>
   *   inline T inc(T $in) { if ($in) { } }
   * </code>
   */
  #[@test]
  public function ifInsideMethodIsNotInlineable() {
    $this->assertEquals(
      array('I403', 'Only one-line return statements can be inlined: inc()'),
      $this->verify(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'in')),
        'body'        => array(
          new IfNode(array('condition' => new VariableNode('in')))
        )
      )))
    );
  }


  /**
   * Tests the following is not inlineable:
   * <code>
   *   inline T inc(T $in) { $in++; return $in; }
   * </code>
   */
  #[@test]
  public function twoLineMethodIsNotInlineable() {
    $this->assertEquals(
      array('I402', 'Only one-liners can be inlined: inc() has 2 statements'),
      $this->verify(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'in')),
        'body'        => array(
          new UnaryOpNode(array('op' => '++', 'postfix' => true, 'expression' => new VariableNode('in'))),
          new ReturnNode(new VariableNode('in'))
        )
      )))
    );
  }
}
