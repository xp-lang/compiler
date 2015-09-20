<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\StaticMethodCallVerification;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\StaticMethodCallNode;

class StaticMethodCallVerificationTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new StaticMethodCallVerification();
  }
  
  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.StaticMethodCallNode call
   * @param   xp.compiler.types.TypeName parent
   * @return  var
   */
  protected function verify(StaticMethodCallNode $call, $parent= null) {
    $scope= new TypeDeclarationScope();
    $scope->declarations[0]= new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('Fixture'),
      $parent ?: new TypeName('lang.Object'),
      null,
      array(
        new MethodNode(array(
          'name'      => 'forName',
          'modifiers' => MODIFIER_STATIC | MODIFIER_PUBLIC
        )),
        new MethodNode(array(
          'name'      => 'getInstance',
          'modifiers' => MODIFIER_STATIC | MODIFIER_PROTECTED
        )),
        new MethodNode(array(
          'name'      => 'asIntern',
          'modifiers' => MODIFIER_STATIC | MODIFIER_PRIVATE
        )),
      )
    );
    $scope->addResolved('parent', $ptr= $scope->resolveType($scope->declarations[0]->parent));
    $scope->addResolved('self', new TypeDeclaration(new ParseTree(null, array(), $scope->declarations[0]), $ptr));
    return $this->fixture->verify($call, $scope);
  }
  
  /**
   * Helper method
   *
   * @param   string type
   * @return  xp.compiler.ast.InstanceCreationNode
   */
  protected function newInstance($type) {
    return new InstanceCreationNode(array('type' => new TypeName($type)));
  }

  #[@test]
  public function nonExistantMethodCall() {
    $this->assertEquals(
      array('T404', 'No such method nonExistant() in Fixture'),
      $this->verify(new StaticMethodCallNode(new TypeName('self'), 'nonExistant'))
    );
  }
  
  #[@test]
  public function thisPublicMethodCall() {
    $this->assertNull(
      $this->verify(new StaticMethodCallNode(new TypeName('self'), 'forName'))
    );
  }

  #[@test]
  public function thisProtectedMethodCall() {
    $this->assertNull(
      $this->verify(new StaticMethodCallNode(new TypeName('self'), 'getInstance'))
    );
  }

  #[@test]
  public function thisPrivateMethodCall() {
    $this->assertNull(
      $this->verify(new StaticMethodCallNode(new TypeName('self'), 'asIntern'))
    );
  }

  #[@test]
  public function classPublicMethodCall() {
    $this->assertNull(
      $this->verify(new StaticMethodCallNode(new TypeName('lang.XPClass'), 'forName'))
    );
  }

  #[@test]
  public function enumProtectedMethodCall() {
    $this->assertEquals(
      array('T403', 'Invoking protected static lang.Enum::membersOf() from Fixture'),
      $this->verify(new StaticMethodCallNode(new TypeName('lang.Enum'), 'membersOf'))
    );
  }

  #[@test]
  public function enumProtectedMethodCallIfSubclass() {
    $this->assertNull(
      $this->verify(new StaticMethodCallNode(new TypeName('lang.Enum'), 'membersOf'), new TypeName('lang.Enum'))
    );
  }
}