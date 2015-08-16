<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\MethodCallVerification;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeDeclaration;
use xp\compiler\types\TypeName;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\CastNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.MethodCallVerification
 */
class MethodCallVerificationTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new MethodCallVerification();
  }
  
  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.MethodCallNode call
   * @param   xp.compiler.types.TypeName parent
   * @return  var
   */
  protected function verify(MethodCallNode $call, $parent= null) {
    $scope= new TypeDeclarationScope();
    $scope->declarations[0]= new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('Fixture'),
      $parent ?: new TypeName('lang.Object'),
      null,
      array(
        new MethodNode(array(
          'name'      => 'hashCode',
          'modifiers' => MODIFIER_PUBLIC
        )),
        new MethodNode(array(
          'name'      => 'asIntern',
          'modifiers' => MODIFIER_PROTECTED
        )),
        new MethodNode(array(
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
  public function nonExistantMethodCall() {
    $this->assertEquals(
      array('T404', 'No such method nonExistant() in Fixture'),
      $this->verify(new MethodCallNode(new VariableNode('this'), 'nonExistant'))
    );
  }
  
  /**
   * Test method call to a public method on this class
   *
   */
  #[@test]
  public function thisPublicMethodCall() {
    $this->assertNull(
      $this->verify(new MethodCallNode(new VariableNode('this'), 'hashCode'))
    );
  }

  /**
   * Test method call to a public method on this class
   *
   */
  #[@test]
  public function thisProtectedMethodCall() {
    $this->assertNull(
      $this->verify(new MethodCallNode(new VariableNode('this'), 'asIntern'))
    );
  }

  /**
   * Test method call to a public method on this class
   *
   */
  #[@test]
  public function thisPrivateMethodCall() {
    $this->assertNull(
      $this->verify(new MethodCallNode(new VariableNode('this'), 'delegate'))
    );
  }

  /**
   * Test method call to a public method on the object class
   *
   */
  #[@test]
  public function objectPublicMethodCall() {
    $this->assertNull(
      $this->verify(new MethodCallNode($this->newInstance('lang.Object'), 'hashCode'))
    );
  }

  /**
   * Test method call to a protected method on the string sclass
   *
   */
  #[@test]
  public function stringProtectedMethodCall() {
    $this->assertEquals(
      array('T403', 'Invoking protected net.xp_lang.tests.StringBuffer::set() from Fixture'),
      $this->verify(new MethodCallNode($this->newInstance('net.xp_lang.tests.StringBuffer'), 'set'))
    );
  }

  /**
   * Test method call to a protected method on the string sclass
   *
   */
  #[@test]
  public function stringProtectedMethodCallIfSubclass() {
    $this->assertNull(
      $this->verify(new MethodCallNode($this->newInstance('net.xp_lang.tests.StringBuffer'), 'set'), new TypeName('net.xp_lang.tests.StringBuffer'))
    );
  }

  /**
   * Test method call on an unsupported type (string)
   *
   */
  #[@test]
  public function unsupportedType() {
    $this->assertEquals(
      array('T305', 'Using member calls on unsupported type string'),
      $this->verify(new MethodCallNode(new StringNode('hello'), 'length'))
    );
  }

  /**
   * Test method call on var type
   *
   */
  #[@test]
  public function varType() {
    $this->assertEquals(
      array('T203', 'Member call (var).length() verification deferred until runtime'),
      $this->verify(new MethodCallNode(new CastNode(new VariableNode('this'), new TypeName('var')), 'length'))
    );
  }
}