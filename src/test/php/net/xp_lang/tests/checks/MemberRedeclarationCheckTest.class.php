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

class MemberRedeclarationCheckTest extends \unittest\TestCase {
  private $fixture;
  private $scope;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new MemberRedeclarationCheck();
    $this->scope= new CompilationUnitScope();
  }
  
  #[@test]
  public function interfaceWithDuplicateMethod() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Runnable::run()'], 
      $this->fixture->verify(
        new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable'), [], [
          new MethodNode(['name' => 'run']),
          new MethodNode(['name' => 'run']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function interfaceWithTwoMethods() {
    $this->assertNull(
      $this->fixture->verify(
        new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable'), [], [
          new MethodNode(['name' => 'run']),
          new MethodNode(['name' => 'runnable']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithDuplicateMethod() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Runner::run()'], 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner'), null, [], [
          new MethodNode(['name' => 'run']),
          new MethodNode(['name' => 'run']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithTwoMethods() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner'), null, [], [
          new MethodNode(['name' => 'run']),
          new MethodNode(['name' => 'runnable']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithDuplicateField() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Runner::$in'], 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner'), null, [], [
          new FieldNode(['name' => 'in']),
          new FieldNode(['name' => 'in']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithFieldAndPropertyWithSameName() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Runner::$in'], 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner'), null, [], [
          new FieldNode(['name' => 'in']),
          new PropertyNode(['name' => 'in']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithTwoFields() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner'), null, [], [
          new FieldNode(['name' => 'in']),
          new FieldNode(['name' => 'out']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithFieldAndMethodWithSameName() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner'), null, [], [
          new FieldNode(['name' => 'run']),
          new MethodNode(['name' => 'run']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithDuplicateConstant() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Std::IN'], 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Std'), null, [], [
          new ClassConstantNode('IN', new TypeName('string'), new StringNode('php://stdin')),
          new ClassConstantNode('IN', new TypeName('string'), new StringNode('php://stdout')),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function enumWithDuplicateMember() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Coin::$penny'], 
      $this->fixture->verify(
        new EnumNode(MODIFIER_PUBLIC, [], new TypeName('Coin'), null, [], [
          new EnumMemberNode(['name' => 'penny']),
          new EnumMemberNode(['name' => 'penny']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function enumWithTwoMembers() {
    $this->assertNull(
      $this->fixture->verify(
        new EnumNode(MODIFIER_PUBLIC, [], new TypeName('Coin'), null, [], [
          new EnumMemberNode(['name' => 'penny']),
          new EnumMemberNode(['name' => 'dime']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function enumWithConflictingFieldAndMember() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Coin::$penny'], 
      $this->fixture->verify(
        new EnumNode(MODIFIER_PUBLIC, [], new TypeName('Coin'), null, [], [
          new EnumMemberNode(['name' => 'penny']),
          new FieldNode(['name' => 'penny']),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithStaticInitializer() {
    $this->assertNull(
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Init'), null, [], [
          new MethodNode(['name' => 'run']),
          new StaticInitializerNode([]),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithStaticInitializerAndConflictingMethod() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Init::__static()'], 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Init'), null, [], [
          new MethodNode(['name' => '__static']),
          new StaticInitializerNode([]),
        ]), 
        $this->scope
      )
    );
  }

  #[@test]
  public function classWithTwoStaticInitializers() {
    $this->assertEquals(
      ['C409', 'Cannot redeclare Init::__static()'], 
      $this->fixture->verify(
        new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Init'), null, [], [
          new StaticInitializerNode([]),
          new StaticInitializerNode([]),
        ]), 
        $this->scope
      )
    );
  }
}