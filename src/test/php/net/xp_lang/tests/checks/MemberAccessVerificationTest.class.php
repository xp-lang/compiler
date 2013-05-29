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

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.MemberAccessVerification
 */
class MemberAccessVerificationTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
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
  protected function verify(MemberAccessNode $call, $parent= null) {
    $scope= new TypeDeclarationScope();
    $scope->declarations[0]= new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('Fixture'),
      $parent ?: new TypeName('lang.Object'),
      null,
      array(
        new FieldNode(array(
          'name'      => 'name',
          'modifiers' => MODIFIER_PUBLIC
        )),
        new FieldNode(array(
          'name'      => 'id',
          'modifiers' => MODIFIER_PROTECTED
        )),
        new FieldNode(array(
          'name'      => 'delegate',
          'modifiers' => MODIFIER_PRIVATE
        )),
      )
    );
    $scope->addResolved('parent', $ptr= $scope->resolveType($scope->declarations[0]->parent));
    $scope->addResolved('self', new TypeDeclaration(new ParseTree(null, array(), $scope->declarations[0]), $ptr));
    $scope->setType(new VariableNode('this'), new TypeName('Fixture'));
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

  /**
   * Test method call to a public method on this class
   *
   */
  #[@test]
  public function nonExistantMemberAccess() {
    $this->assertEquals(
      array('T404', 'No such field $nonExistant in Fixture'),
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'nonExistant'))
    );
  }
  
  /**
   * Test method call to a public method on this class
   *
   */
  #[@test]
  public function thisPublicMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'name'))
    );
  }

  /**
   * Test method call to a public method on this class
   *
   */
  #[@test]
  public function thisProtectedMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'id'))
    );
  }

  /**
   * Test method call to a public method on this class
   *
   */
  #[@test]
  public function thisPrivateMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode(new VariableNode('this'), 'delegate'))
    );
  }

  /**
   * Test method call to a public method on the object class
   *
   */
  #[@test]
  public function integerPublicMemberAccess() {
    $this->assertNull(
      $this->verify(new MemberAccessNode($this->newInstance('lang.types.Integer'), 'value'))
    );
  }

  /**
   * Test method call to a protected method on the string sclass
   *
   */
  #[@test]
  public function stringProtectedMemberAccess() {
    $this->assertEquals(
      array('T403', 'Accessing protected lang.types.String::$buffer from Fixture'),
      $this->verify(new MemberAccessNode($this->newInstance('lang.types.String'), 'buffer'))
    );
  }

  /**
   * Test method call to a protected method on the string sclass
   *
   */
  #[@test]
  public function stringProtectedMemberAccessIfSubclass() {
    $this->assertNull(
      $this->verify(new MemberAccessNode($this->newInstance('lang.types.String'), 'buffer'), new TypeName('lang.types.String'))
    );
  }

  /**
   * Test method call on an unsupported type (string)
   *
   */
  #[@test]
  public function unsupportedType() {
    $this->assertEquals(
      array('T305', 'Using member access on unsupported type string'),
      $this->verify(new MemberAccessNode(new StringNode('hello'), 'length'))
    );
  }

  /**
   * Test method call on var type
   *
   */
  #[@test]
  public function varType() {
    $this->assertEquals(
      array('T203', 'Member access (var).length() verification deferred until runtime'),
      $this->verify(new MemberAccessNode(new CastNode(new VariableNode('this'), new TypeName('var')), 'length'))
    );
  }
}