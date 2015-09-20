<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\TypeMemberHasDocumentation;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\TypeDeclarationNode;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\InterfaceNode;
use xp\compiler\ast\EnumNode;
use xp\compiler\ast\RoutineNode;
use xp\compiler\ast\MethodNode;

class TypeMemberHasDocumentationTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new TypeMemberHasDocumentation();
  }

  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.RoutineNode routine
   * @param   xp.compiler.ast.TypeDeclarationNode type
   * @return  var
   */
  protected function verify(RoutineNode $routine, TypeDeclarationNode $type) {
    $scope= new TypeDeclarationScope();
    $scope->declarations[0]= $type;
    return $this->fixture->verify($routine, $scope);
  }
  
  #[@test]
  public function methodWithoutApidoc() {
    $m= new MethodNode(array(
      'name'        => 'run',
      'modifiers'   => MODIFIER_ABSTRACT,
      'returns'     => TypeName::$VOID,
      'parameters'  => array(),
      'body'        => array()
    ));
    $this->assertEquals(
      array('D201', 'No api doc for member Runner::run'), 
      $this->verify($m, new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner')))
    );
  }

  #[@test]
  public function methodsInSyntheticClassesNotChecked() {
    $c= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Lambda··4b70075bd9164'));
    $c->synthetic= true;
    $m= new MethodNode(array(
      'name'        => 'run',
      'modifiers'   => MODIFIER_ABSTRACT,
      'returns'     => TypeName::$VOID,
      'parameters'  => array(),
      'body'        => array()
    ));
    $this->assertNull($this->verify($m, $c));
  }
}