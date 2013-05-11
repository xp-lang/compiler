<?php namespace net\xp_lang\tests\optimization;

use xp\compiler\optimize\Optimizations;
use xp\compiler\optimize\InlineMethodCalls;
use xp\compiler\optimize\InlineStaticMethodCalls;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\ClassNode;
use xp\compiler\types\TypeName;
use xp\compiler\types\MethodScope;
use xp\compiler\types\TypeDeclaration;

/**
 * TestCase for Inlining operations
 *
 * @see      xp://xp.compiler.optimize.InliningOptimization
 */
class InliningOptimizationTest extends \unittest\TestCase {
  protected $fixture = null;
  protected $scope = null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new Optimizations();
    $this->fixture->add(new InlineMethodCalls());
    $this->fixture->add(new InlineStaticMethodCalls());
    
    // Declare class
    $class= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Test'), null, array(), array());
    
    // Declare scope and inject resolved types
    $this->scope= new MethodScope();
    $this->scope->declarations[0]= $class;
    $this->scope->setType(new VariableNode('this'), $class->name);
    $this->scope->addResolved('self', new TypeDeclaration(new ParseTree('', array(), $class)));
  }
  
  /**
   * Wrapper around fixture's optimize() method
   *
   * @param   xp.compiler.ast.Node call
   * @param   xp.compiler.ast.MethodNode[] declarations
   * @return  xp.compiler.ast.Node
   */
  protected function optimize($call, $declarations) {
    $this->scope->declarations[0]->body= $declarations;
    return $this->fixture->optimize($call, $this->scope);
  }
  
  /**
   * Test instance methods
   */
  #[@test]
  public function oneLineInstanceMethod() {
    $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
    $this->assertEquals(
      new UnaryOpNode(array('op' => '++', 'postfix' => false, 'expression' => new VariableNode('a'))), 
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'in')),
        'body'        => array(
          new ReturnNode(
            new UnaryOpNode(array('op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')))
          )
        )
      ))))
    );
  }

  /**
   * Test instance methods
   */
  #[@test]
  public function noInstanceMethodOptimizationWithoutInlineFlag() {
    $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
    $this->assertEquals(
      $call, 
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => 0,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'in')),
        'body'        => array(
          new ReturnNode(
            new UnaryOpNode(array('op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')))
          )
        )
      ))))
    );
  }

  /**
   * Test static methods
   */
  #[@test]
  public function oneLineStaticMethod() {
    $call= new StaticMethodCallNode(new TypeName('self'), 'inc', array(new VariableNode('a')));
    $this->assertEquals(
      new UnaryOpNode(array('op' => '++', 'postfix' => false, 'expression' => new VariableNode('a'))), 
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE | MODIFIER_STATIC,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'in')),
        'body'        => array(
          new ReturnNode(
            new UnaryOpNode(array('op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')))
          )
        )
      ))))
    );
  }

  /**
   * Test static methods
   */
  #[@test]
  public function noStaticMethodOptimizationWithoutInlineFlag() {
    $call= new StaticMethodCallNode(new TypeName('self'), 'inc', array(new VariableNode('a')));
    $this->assertEquals(
      $call, 
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => MODIFIER_STATIC,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'in')),
        'body'        => array(
          new ReturnNode(
            new UnaryOpNode(array('op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')))
          )
        )
      ))))
    );
  }

  /**
   * Test parameter rewriting with the following declaration:
   * <code>
   *   inline T add(T $x, T $y) { return $x + $y; }
   * </code>
   */
  #[@test]
  public function parameterRewritingWithTwoParameters() {
    $call= new MethodCallNode(new VariableNode('this'), 'add', array(new VariableNode('a'), new VariableNode('b')));
    $this->assertEquals(
      new BinaryOpNode(array('lhs' => new VariableNode('a'), 'rhs' => new VariableNode('b'), 'op' => '+')), 
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'add',
        'parameters'  => array(array('name' => 'x'), array('name' => 'y')),
        'body'        => array(
          new ReturnNode(
            new BinaryOpNode(array('lhs' => new VariableNode('x'), 'rhs' => new VariableNode('y'), 'op' => '+'))
          )
        )
      ))))
    );
  }

  /**
   * Test parameter rewriting with the following declaration:
   * <code>
   *   inline T inc(T $x) { return $x + $this.step; }
   * </code>
   */
  #[@test]
  public function parameterRewritingWithOneParameterAndOneMember() {
    $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
    $this->assertEquals(
      new BinaryOpNode(array('lhs' => new VariableNode('a'), 'rhs' => new MemberAccessNode(new VariableNode('this'), 'step'), 'op' => '+')), 
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'x')),
        'body'        => array(
          new ReturnNode(
            new BinaryOpNode(array('lhs' => new VariableNode('x'), 'rhs' => new MemberAccessNode(new VariableNode('this'), 'step'), 'op' => '+'))
          )
        )
      ))))
    );
  }

  /**
   * Test recursion is not optimized
   * <code>
   *   inline T inc(T $x) { return $this.inc($x); }
   * </code>
   */
  #[@test]
  public function recursionsNotOptimized() {
    $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
    $this->assertEquals(
      $call,
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'x')),
        'body'        => array(
          new ReturnNode(
            new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('x')))
          )
        )
      ))))
    );
  }

  /**
   * Test recursion is optimized recursively
   * <code>
   *   inline T inc(T $x) { return $x + $this.inc($x); }
   *   $a= $this.inc($a);       // Original
   *   $a= $a + $this.inc($a);  // Optimized
   * </code>
   */
  #[@test]
  public function recursionsNotOptimizedRecursively() {
    $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
    $this->assertEquals(
      new BinaryOpNode(array('lhs' => new VariableNode('a'), 'rhs' => $call, 'op' => '+')),
      $this->optimize($call, array(new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'x')),
        'body'        => array(
          new ReturnNode(
            new BinaryOpNode(array('lhs' => new VariableNode('x'), 'rhs' => new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('x'))), 'op' => '+'))
          )
        )
      ))))
    );
  }

  /**
   * Test recursion is optimized recursively
   * <code>
   *   inline T add(T $x, T $y) { return $x + $y; }
   *   inline T inc(T $m) { return $this.add($m, $this.step); }
   *   inline T dec(T $m) { return $this.add($m, -$this.step); }
   *   $a= $this.inc($a);       // Original
   *   $a= $a + $this.step;     // Optimized
   * </code>
   */
  #[@test]
  public function cascadedInlining() {
    $member= new MemberAccessNode(new VariableNode('this'), 'step');
    $unary= new UnaryOpNode(array('op' => '-', 'postfix' => false, 'expression' => $member));
    $decl= array(
      new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'add',
        'parameters'  => array(array('name' => 'x'), array('name' => 'y')),
        'body'        => array(
          new ReturnNode(
            new BinaryOpNode(array('lhs' => new VariableNode('x'), 'rhs' => new VariableNode('y'), 'op' => '+'))
          )
        )
      )),   
      new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => array(array('name' => 'm')),
        'body'        => array(
          new ReturnNode(new MethodCallNode(new VariableNode('this'), 'add', array(
            new VariableNode('m'), 
            $member
          )))
        )
      )),   
      new MethodNode(array(
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'dec',
        'parameters'  => array(array('name' => 'm')),
        'body'        => array(
          new ReturnNode(new MethodCallNode(new VariableNode('this'), 'add', array(
            new VariableNode('m'), 
            $unary
          )))
        )
      )),   
    );

    $this->assertEquals(
      new BinaryOpNode(array('lhs' => new VariableNode('a'), 'rhs' => $member, 'op' => '+')),
      $this->optimize(new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a'))), $decl),
      'inc'
    );
    $this->assertEquals(
      new BinaryOpNode(array('lhs' => new VariableNode('a'), 'rhs' => $unary, 'op' => '+')),
      $this->optimize(new MethodCallNode(new VariableNode('this'), 'dec', array(new VariableNode('a'))), $decl),
      'dec'
    );
  }
}
