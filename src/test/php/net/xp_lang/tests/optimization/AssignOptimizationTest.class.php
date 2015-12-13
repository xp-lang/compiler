<?php namespace net\xp_lang\tests\optimization;

use xp\compiler\optimize\Optimizations;
use xp\compiler\optimize\AssignOptimization;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\StaticMemberAccessNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\types\MethodScope;
use xp\compiler\types\TypeName;

/**
 * TestCase for binary operations
 *
 * @see      xp://xp.compiler.optimize.AssignOptimization
 */
class AssignOptimizationTest extends \unittest\TestCase {
  protected $fixture = null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new Optimizations();
    $this->fixture->add(new AssignOptimization());
  }
  
  /**
   * Wrapper around fixture's optimize() method
   *
   * @param   xp.compiler.ast.AssignmentNode
   * @return  xp.compiler.ast.Node
   */
  protected function optimize($assignment) {
    return $this->fixture->optimize($assignment, new MethodScope());
  }
  
  /**
   * Test optimizing $a= $a + $b; to $a+= $b;
   */
  #[@test]
  public function addition() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '+=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '+', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a - $b; to $a-= $b;
   */
  #[@test]
  public function subtraction() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '-=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '-', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a * $b; to $a*= $b;
   */
  #[@test]
  public function multiplication() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '*=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '*', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a / $b; to $a/= $b;
   */
  #[@test]
  public function division() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '/=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '/', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a % $b; to $a%= $b;
   */
  #[@test]
  public function modulo() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '%=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '%', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a ~ $b; to $a~= $b;
   */
  #[@test]
  public function concat() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '~=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '~', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a >> $b; to $a>>= $b;
   */
  #[@test]
  public function shiftRight() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '>>=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '>>', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a << $b; to $a<<= $b;
   */
  #[@test]
  public function shiftLeft() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '<<=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '<<', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a & $b; to $a&= $b;
   */
  #[@test]
  public function logicalAnd() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '&=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '&', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a | $b; to $a|= $b;
   */
  #[@test]
  public function logicalOr() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '|=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '|', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test optimizing $a= $a ^ $b; to $a^= $b;
   */
  #[@test]
  public function logicalXor() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new VariableNode('a'), 'op' => '^=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '^', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test $this.a= $this.a + $b; is optimized
   */
  #[@test]
  public function instanceMemberVariables() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new MemberAccessNode(new VariableNode('this'), 'a'), 'op' => '+=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new MemberAccessNode(new VariableNode('this'), 'a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new MemberAccessNode(new VariableNode('this'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test self::a= self::a + $b; is optimized
   */
  #[@test]
  public function staticMemberVariables() {
    $this->assertEquals(
      new AssignmentNode(['variable' => new StaticMemberAccessNode(new TypeName('self'), 'a'), 'op' => '+=', 'expression' => new VariableNode('b')]),
      $this->optimize(new AssignmentNode([
        'variable'   => new StaticMemberAccessNode(new TypeName('self'), 'a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(['lhs' => new StaticMemberAccessNode(new TypeName('self'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test $this.a= $a + $b; is not optimized
   */
  #[@test]
  public function notOptimizedIfInstanceMemberAssignToLocal() {
    $assignment= new AssignmentNode([
      'variable'   => new MemberAccessNode(new VariableNode('this'), 'a'), 
      'op'         => '=', 
      'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '+', 'rhs' => new VariableNode('b')])
    ]);
    $this->assertEquals($assignment, $this->optimize($assignment));
  }

  /**
   * Test $a= $this.a + $b; is not optimized
   */
  #[@test]
  public function notOptimizedIfLocalAssignToInstanceMember() {
    $assignment= new AssignmentNode([
      'variable'   => new VariableNode('a'), 
      'op'         => '=', 
      'expression' => new BinaryOpNode(['lhs' => new MemberAccessNode(new VariableNode('this'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')])
    ]);
    $this->assertEquals($assignment, $this->optimize($assignment));
  }

  /**
   * Test self::a= $a + $b; is not optimized
   */
  #[@test]
  public function notOptimizedIfStaticMemberAssignToLocal() {
    $assignment= new AssignmentNode([
      'variable'   => new StaticMemberAccessNode(new TypeName('self'), 'a'), 
      'op'         => '=', 
      'expression' => new BinaryOpNode(['lhs' => new VariableNode('a'), 'op' => '+', 'rhs' => new VariableNode('b')])
    ]);
    $this->assertEquals($assignment, $this->optimize($assignment));
  }

  /**
   * Test $a= self::a + $b; is not optimized
   */
  #[@test]
  public function notOptimizedIfLocalAssignToStaticMember() {
    $assignment= new AssignmentNode([
      'variable'   => new VariableNode('a'), 
      'op'         => '=', 
      'expression' => new BinaryOpNode(['lhs' => new StaticMemberAccessNode(new TypeName('self'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')])
    ]);
    $this->assertEquals($assignment, $this->optimize($assignment));
  }

  /**
   * Test $a= $b + $c is not optimized
   */
  #[@test]
  public function notOptimizedIfNotLHS() {
    $assignment= new AssignmentNode([
      'variable'   => new VariableNode('a'), 
      'op'         => '=', 
      'expression' => new BinaryOpNode(['lhs' => new VariableNode('b'), 'op' => '+', 'rhs' => new VariableNode('c')])
    ]);
    $this->assertEquals($assignment, $this->optimize($assignment));
  }

  /**
   * Test $a= $b + $a is not optimized
   */
  #[@test]
  public function notOptimizedIfRHS() {
    $assignment= new AssignmentNode([
      'variable'   => new VariableNode('a'), 
      'op'         => '=', 
      'expression' => new BinaryOpNode(['lhs' => new VariableNode('b'), 'op' => '+', 'rhs' => new VariableNode('a')])
    ]);
    $this->assertEquals($assignment, $this->optimize($assignment));
  }

  /**
   * Test $a-= -$b; is optimized to $a+= $b;
   */
  #[@test]
  public function minusAssignAndUnaryMinus() {
    $this->assertEquals(
      new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '+=', 
        'expression' => new VariableNode('b')
      ]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '-=', 
        'expression' => new UnaryOpNode(['op' => '-', 'postfix' => false, 'expression' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test $a+= -$b; is optimized to $a-= $b;
   */
  #[@test]
  public function plusAssignAndUnaryMinus() {
    $this->assertEquals(
      new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '-=', 
        'expression' => new VariableNode('b')
      ]),
      $this->optimize(new AssignmentNode([
        'variable'   => new VariableNode('a'), 
        'op'         => '+=', 
        'expression' => new UnaryOpNode(['op' => '-', 'postfix' => false, 'expression' => new VariableNode('b')])
      ]))
    );
  }

  /**
   * Test $a+= -$b; is optimized to $a-= $b;
   */
  #[@test]
  public function timesAssignAndUnaryMinusNotOptimized() {
    $assignment= new AssignmentNode([
      'variable'   => new VariableNode('a'), 
      'op'         => '*=', 
      'expression' => new UnaryOpNode(['op' => '-', 'postfix' => false, 'expression' => new VariableNode('b')])
    ]);
    $this->assertEquals($assignment, $this->optimize($assignment));
  }
}
