<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\RoutinesVerification;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\TypeDeclarationNode;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\InterfaceNode;
use xp\compiler\ast\RoutineNode;
use xp\compiler\ast\MethodNode;

class RoutinesVerificationTest extends \unittest\TestCase {
  private $fixture;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new RoutinesVerification();
  }
  
  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.RoutineNode routine
   * @param   xp.compiler.ast.TypeDeclarationNode type
   * @return  var
   */
  private function verify(RoutineNode $routine, TypeDeclarationNode $type) {
    $scope= new TypeDeclarationScope();
    $scope->declarations[0]= $type;
    return $this->fixture->verify($routine, $scope);
  }
  
  #[@test]
  public function interfaceMethodsMayNotHaveBodies() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => MODIFIER_PUBLIC,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => []
    ]);
    $this->assertEquals(
      ['R403', 'Interface methods may not have a body Runnable::run'], 
      $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable')))
    );
  }

  #[@test]
  public function interfaceMethodsMayNotBePrivate() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => MODIFIER_PRIVATE,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => null
    ]);
    $this->assertEquals(
      ['R401', 'Interface methods may only be public Runnable::run'], 
      $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable')))
    );
  }

  #[@test]
  public function interfaceMethodsMayNotBeProtected() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => MODIFIER_PROTECTED,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => null
    ]);
    $this->assertEquals(
      ['R401', 'Interface methods may only be public Runnable::run'], 
      $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable')))
    );
  }

  #[@test]
  public function interfaceMethodsMayNotBeAbstract() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => MODIFIER_ABSTRACT,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => null
    ]);
    $this->assertEquals(
      ['R401', 'Interface methods may only be public Runnable::run'], 
      $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable')))
    );
  }

  #[@test]
  public function interfaceMethodsMayNotBeFinal() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => MODIFIER_FINAL,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => null
    ]);
    $this->assertEquals(
      ['R401', 'Interface methods may only be public Runnable::run'], 
      $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable')))
    );
  }

  #[@test]
  public function interfaceMethodsMayOmitModifier() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => 0,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => null
    ]);
    $this->assertNull(
      $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Runnable')))
    );
  }

  #[@test]
  public function abstractMethodsMayNotHaveBodies() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => MODIFIER_ABSTRACT,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => []
    ]);
    $this->assertEquals(
      ['R403', 'Abstract methods may not have a body Runner::run'], 
      $this->verify($m, new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner')))
    );
  }

  #[@test]
  public function nonAbstractMethodsMustHaveBodies() {
    $m= new MethodNode([
      'name'        => 'run',
      'modifiers'   => MODIFIER_PUBLIC,
      'returns'     => TypeName::$VOID,
      'parameters'  => [],
      'body'        => null
    ]);
    $this->assertEquals(
      ['R401', 'Non-abstract methods must have a body Runner::run'], 
      $this->verify($m, new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner')))
    );
  }

  #[@test]
  public function extensionMethodsMustBeStatic() {
    $m= new MethodNode([
      'name'        => 'equal',
      'modifiers'   => MODIFIER_PUBLIC,
      'returns'     => TypeName::$VOID,
      'parameters'  => [
        ['name' => 'in', 'type' => new TypeName('string')],
        ['name' => 'cmp', 'type' => new TypeName('string')],
      ],
      'extension'   => true,
      'body'        => []
    ]);
    $this->assertEquals(
      ['E403', 'Extension methods must be static Runner::equal'], 
      $this->verify($m, new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner')))
    );
  }

  #[@test]
  public function extensionMethods() {
    $m= new MethodNode([
      'name'        => 'equal',
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'returns'     => TypeName::$VOID,
      'parameters'  => [
        ['name' => 'in', 'type' => new TypeName('string')],
        ['name' => 'cmp', 'type' => new TypeName('string')],
      ],
      'extension'   => true,
      'body'        => []
    ]);
    $this->assertNull(
      $this->verify($m, new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Runner')))
    );
  }
}
