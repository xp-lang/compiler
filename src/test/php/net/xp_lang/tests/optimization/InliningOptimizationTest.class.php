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
    $class= new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Test'), null, [], []);
    
    // Declare scope and inject resolved types
    $this->scope= new MethodScope();
    $this->scope->declarations[0]= $class;
    $this->scope->setType(new VariableNode('this'), $class->name);
    $this->scope->addResolved('self', new TypeDeclaration(new ParseTree('', [], $class)));
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
    $call= new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('a')]);
    $this->assertEquals(
      new UnaryOpNode(['op' => '++', 'postfix' => false, 'expression' => new VariableNode('a')]), 
      $this->optimize($call, [new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => [['name' => 'in']],
        'body'        => [
          new ReturnNode(
            new UnaryOpNode(['op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')])
          )
        ]
      ])])
    );
  }

  /**
   * Test instance methods
   */
  #[@test]
  public function noInstanceMethodOptimizationWithoutInlineFlag() {
    $call= new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('a')]);
    $this->assertEquals(
      $call, 
      $this->optimize($call, [new MethodNode([
        'modifiers'   => 0,
        'name'        => 'inc',
        'parameters'  => [['name' => 'in']],
        'body'        => [
          new ReturnNode(
            new UnaryOpNode(['op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')])
          )
        ]
      ])])
    );
  }

  /**
   * Test static methods
   */
  #[@test]
  public function oneLineStaticMethod() {
    $call= new StaticMethodCallNode(new TypeName('self'), 'inc', [new VariableNode('a')]);
    $this->assertEquals(
      new UnaryOpNode(['op' => '++', 'postfix' => false, 'expression' => new VariableNode('a')]), 
      $this->optimize($call, [new MethodNode([
        'modifiers'   => MODIFIER_INLINE | MODIFIER_STATIC,
        'name'        => 'inc',
        'parameters'  => [['name' => 'in']],
        'body'        => [
          new ReturnNode(
            new UnaryOpNode(['op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')])
          )
        ]
      ])])
    );
  }

  /**
   * Test static methods
   */
  #[@test]
  public function noStaticMethodOptimizationWithoutInlineFlag() {
    $call= new StaticMethodCallNode(new TypeName('self'), 'inc', [new VariableNode('a')]);
    $this->assertEquals(
      $call, 
      $this->optimize($call, [new MethodNode([
        'modifiers'   => MODIFIER_STATIC,
        'name'        => 'inc',
        'parameters'  => [['name' => 'in']],
        'body'        => [
          new ReturnNode(
            new UnaryOpNode(['op' => '++', 'postfix' => false, 'expression' => new VariableNode('in')])
          )
        ]
      ])])
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
    $call= new MethodCallNode(new VariableNode('this'), 'add', [new VariableNode('a'), new VariableNode('b')]);
    $this->assertEquals(
      new BinaryOpNode(['lhs' => new VariableNode('a'), 'rhs' => new VariableNode('b'), 'op' => '+']), 
      $this->optimize($call, [new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'add',
        'parameters'  => [['name' => 'x'], ['name' => 'y']],
        'body'        => [
          new ReturnNode(
            new BinaryOpNode(['lhs' => new VariableNode('x'), 'rhs' => new VariableNode('y'), 'op' => '+'])
          )
        ]
      ])])
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
    $call= new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('a')]);
    $this->assertEquals(
      new BinaryOpNode(['lhs' => new VariableNode('a'), 'rhs' => new MemberAccessNode(new VariableNode('this'), 'step'), 'op' => '+']), 
      $this->optimize($call, [new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => [['name' => 'x']],
        'body'        => [
          new ReturnNode(
            new BinaryOpNode(['lhs' => new VariableNode('x'), 'rhs' => new MemberAccessNode(new VariableNode('this'), 'step'), 'op' => '+'])
          )
        ]
      ])])
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
    $call= new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('a')]);
    $this->assertEquals(
      $call,
      $this->optimize($call, [new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => [['name' => 'x']],
        'body'        => [
          new ReturnNode(
            new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('x')])
          )
        ]
      ])])
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
    $call= new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('a')]);
    $this->assertEquals(
      new BinaryOpNode(['lhs' => new VariableNode('a'), 'rhs' => $call, 'op' => '+']),
      $this->optimize($call, [new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => [['name' => 'x']],
        'body'        => [
          new ReturnNode(
            new BinaryOpNode(['lhs' => new VariableNode('x'), 'rhs' => new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('x')]), 'op' => '+'])
          )
        ]
      ])])
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
    $unary= new UnaryOpNode(['op' => '-', 'postfix' => false, 'expression' => $member]);
    $decl= [
      new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'add',
        'parameters'  => [['name' => 'x'], ['name' => 'y']],
        'body'        => [
          new ReturnNode(
            new BinaryOpNode(['lhs' => new VariableNode('x'), 'rhs' => new VariableNode('y'), 'op' => '+'])
          )
        ]
      ]),   
      new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'inc',
        'parameters'  => [['name' => 'm']],
        'body'        => [
          new ReturnNode(new MethodCallNode(new VariableNode('this'), 'add', [
            new VariableNode('m'), 
            $member
          ]))
        ]
      ]),   
      new MethodNode([
        'modifiers'   => MODIFIER_INLINE,
        'name'        => 'dec',
        'parameters'  => [['name' => 'm']],
        'body'        => [
          new ReturnNode(new MethodCallNode(new VariableNode('this'), 'add', [
            new VariableNode('m'), 
            $unary
          ]))
        ]
      ]),   
    ];

    $this->assertEquals(
      new BinaryOpNode(['lhs' => new VariableNode('a'), 'rhs' => $member, 'op' => '+']),
      $this->optimize(new MethodCallNode(new VariableNode('this'), 'inc', [new VariableNode('a')]), $decl),
      'inc'
    );
    $this->assertEquals(
      new BinaryOpNode(['lhs' => new VariableNode('a'), 'rhs' => $unary, 'op' => '+']),
      $this->optimize(new MethodCallNode(new VariableNode('this'), 'dec', [new VariableNode('a')]), $decl),
      'dec'
    );
  }
}
