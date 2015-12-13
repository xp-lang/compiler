<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\MemberAccessVerification;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeDeclaration;
use xp\compiler\types\TypeName;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\CastNode;

class MemberAccessVerificationTest extends \unittest\TestCase {
  private $fixture;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new MemberAccessVerification();
  }
  
  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.MemberAccessNode call
   * @param   xp.compiler.types.TypeName parent
   * @return  var
   */
  private function verify(MemberAccessNode $call, $parent= null) {
    $scope= new TypeDeclarationScope();
    $scope->declarations[0]= new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('Fixture'),
      $parent ?: new TypeName('lang.Object'),
      null,
      [
        new FieldNode([
          'name'      => 'name',
          'modifiers' => MODIFIER_PUBLIC
        ]),
        new FieldNode([
          'name'      => 'id',
          'modifiers' => MODIFIER_PROTECTED
        ]),
        new FieldNode([
          'name'      => 'delegate',
          'modifiers' => MODIFIER_PRIVATE
        ]),
      ]
    );
    $scope->addResolved('parent', $ptr= $scope->resolveType($scope->declarations[0]->parent));
    $scope->addResolved('self', new TypeDeclaration(new ParseTree(null, [], $scope->declarations[0]), $ptr));
    $scope->setType(new VariableNode('this'), new TypeName('Fixture'));
    return $this->fixture->verify($call, $scope);
  }
  
  /**
   * Helper method
   *
   * @param   string type
   * @return  xp.compiler.ast.InstanceCreationNode
   */
  private function newInstance($type) {
    return new InstanceCreationNode(['type' => new TypeName($type)]);
  }

  #[@test]
  public function nonExistantMemberAccess() {
    $this->assertEquals(
      ['T404', 'No such field $nonExistant in Fixture'],
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'nonExistant'))
    );
  }
  
  #[@test]
  public function thisPublicMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'name'))
    );
  }

  #[@test]
  public function thisProtectedMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'id'))
    );
  }

  #[@test]
  public function thisPrivateMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'delegate'))
    );
  }

  #[@test]
  public function integerPublicMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode($this->newInstance('lang.types.Integer'), 'value'))
    );
  }

  #[@test]
  public function stringProtectedMemberAccess() {
    $this->assertEquals(
      ['T403', 'Accessing protected net.xp_lang.tests.StringBuffer::$buffer from Fixture'],
      $this->verify(new MemberAccessNode($this->newInstance('net.xp_lang.tests.StringBuffer'), 'buffer'))
    );
  }

  #[@test]
  public function stringProtectedMemberAccessIfSubclass() {
    $this->assertNull(
      $this->verify(new MemberAccessNode($this->newInstance('net.xp_lang.tests.StringBuffer'), 'buffer'), new TypeName('net.xp_lang.tests.StringBuffer'))
    );
  }

  #[@test]
  public function unsupportedType() {
    $this->assertEquals(
      ['T305', 'Using member access on unsupported type string'],
      $this->verify(new MemberAccessNode(new StringNode('hello'), 'length'))
    );
  }

  #[@test]
  public function varType() {
    $this->assertEquals(
      ['T203', 'Member access (var).length() verification deferred until runtime'],
      $this->verify(new MemberAccessNode(new CastNode(new VariableNode('this'), new TypeName('var')), 'length'))
    );
  }
}