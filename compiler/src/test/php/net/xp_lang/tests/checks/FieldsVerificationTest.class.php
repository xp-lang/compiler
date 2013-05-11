<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\FieldsVerification;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\InterfaceNode;
use xp\compiler\ast\TypeDeclarationNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.FieldsVerification
 */
class FieldsVerificationTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new FieldsVerification();
  }
  
  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.FieldNode field
   * @param   xp.compiler.ast.TypeDeclarationNode type
   * @return  var
   */
  protected function verify(FieldNode $field, TypeDeclarationNode $type) {
    $scope= new TypeDeclarationScope();
    $scope->declarations[0]= $type;
    return $this->fixture->verify($field, $scope);
  }
  
  /**
   * Test fields inside classes
   *
   */
  #[@test]
  public function classesMayHaveFieldDeclarations() {
    $f= new FieldNode(array(
      'name'        => 'color',
      'modifiers'   => MODIFIER_PUBLIC,
      'type'        => new TypeName('string'),
    ));
    $this->assertEquals(
      array('I403', 'Interfaces may not have field declarations'), 
      $this->verify($f, new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Item')))
    );
  }

  /**
   * Test fields inside classes
   *
   */
  #[@test]
  public function interfacesMayNotHaveFieldDeclarations() {
    $f= new FieldNode(array(
      'name'        => 'color',
      'modifiers'   => MODIFIER_PUBLIC,
      'type'        => new TypeName('string'),
    ));
    $this->assertNull(
      $this->verify($f, new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Item')))
    );
  }
}