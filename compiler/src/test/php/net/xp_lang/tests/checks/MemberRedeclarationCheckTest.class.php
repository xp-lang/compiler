<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\MemberRedeclarationCheck;
use xp\compiler\types\CompilationUnitScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\InterfaceNode;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\EnumNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\PropertyNode;
use xp\compiler\ast\ClassConstantNode;
use xp\compiler\ast\EnumMemberNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\StaticInitializerNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.MemberRedeclarationCheck
 */
class MemberRedeclarationCheckTest extends \unittest\TestCase {
  protected $fixture= null;
  protected $scope= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new MemberRedeclarationCheck();
    $this->scope= new CompilationUnitScope();
  }
  
  /**
   * Test interface
   *
   */
  #[@test]
  public function interfaceWithDuplicateMethod() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Runnable::run()'), 
      $this->fixture->verify(
        new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable'), array(), array(
          new MethodNode(array('name' => 'run')),
          new MethodNode(array('name' => 'run')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test interface
   *
   */
  #[@test]
  public function interfaceWithTwoMethods() {
    $this->assertNull(
      $this->fixture->verify(
        new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable'), array(), array(
          new MethodNode(array('name' => 'run')),
          new MethodNode(array('name' => 'runnable')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class
   *
   */
  #[@test]
  public function classWithDuplicateMethod() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Runner::run()'), 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'), null, array(), array(
          new MethodNode(array('name' => 'run')),
          new MethodNode(array('name' => 'run')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class
   *
   */
  #[@test]
  public function classWithTwoMethods() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'), null, array(), array(
          new MethodNode(array('name' => 'run')),
          new MethodNode(array('name' => 'runnable')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class
   *
   */
  #[@test]
  public function classWithDuplicateField() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Runner::$in'), 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'), null, array(), array(
          new FieldNode(array('name' => 'in')),
          new FieldNode(array('name' => 'in')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class
   *
   */
  #[@test]
  public function classWithFieldAndPropertyWithSameName() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Runner::$in'), 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'), null, array(), array(
          new FieldNode(array('name' => 'in')),
          new PropertyNode(array('name' => 'in')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class
   *
   */
  #[@test]
  public function classWithTwoFields() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'), null, array(), array(
          new FieldNode(array('name' => 'in')),
          new FieldNode(array('name' => 'out')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class
   *
   */
  #[@test]
  public function classWithFieldAndMethodWithSameName() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'), null, array(), array(
          new FieldNode(array('name' => 'run')),
          new MethodNode(array('name' => 'run')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class
   *
   */
  #[@test]
  public function classWithDuplicateConstant() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Std::IN'), 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Std'), null, array(), array(
          new ClassConstantNode('IN', new TypeName('string'), new StringNode('php://stdin')),
          new ClassConstantNode('IN', new TypeName('string'), new StringNode('php://stdout')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test enum
   *
   */
  #[@test]
  public function enumWithDuplicateMember() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Coin::$penny'), 
      $this->fixture->verify(
        new EnumNode(MODIFIER_PUBLIC, array(), new TypeName('Coin'), null, array(), array(
          new EnumMemberNode(array('name' => 'penny')),
          new EnumMemberNode(array('name' => 'penny')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test enum
   *
   */
  #[@test]
  public function enumWithTwoMembers() {
    $this->assertNull(
      $this->fixture->verify(
        new EnumNode(MODIFIER_PUBLIC, array(), new TypeName('Coin'), null, array(), array(
          new EnumMemberNode(array('name' => 'penny')),
          new EnumMemberNode(array('name' => 'dime')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test enum
   *
   */
  #[@test]
  public function enumWithConflictingFieldAndMember() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Coin::$penny'), 
      $this->fixture->verify(
        new EnumNode(MODIFIER_PUBLIC, array(), new TypeName('Coin'), null, array(), array(
          new EnumMemberNode(array('name' => 'penny')),
          new FieldNode(array('name' => 'penny')),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class with static initializer
   *
   */
  #[@test]
  public function classWithStaticInitializer() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Init'), null, array(), array(
          new MethodNode(array('name' => 'run')),
          new StaticInitializerNode(array()),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class with static initializer and __static() method
   *
   */
  #[@test]
  public function classWithStaticInitializerAndConflictingMethod() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Init::__static()'), 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Init'), null, array(), array(
          new MethodNode(array('name' => '__static')),
          new StaticInitializerNode(array()),
        )), 
        $this->scope
      )
    );
  }

  /**
   * Test class with static initializers
   *
   */
  #[@test]
  public function classWithTwoStaticInitializers() {
    $this->assertEquals(
      array('C409', 'Cannot redeclare Init::__static()'), 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Init'), null, array(), array(
          new StaticInitializerNode(array()),
          new StaticInitializerNode(array()),
        )), 
        $this->scope
      )
    );
  }
}
