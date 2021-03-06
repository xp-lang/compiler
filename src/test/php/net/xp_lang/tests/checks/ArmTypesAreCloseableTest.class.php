<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\ArmTypesAreCloseable;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\ArmNode;

class ArmTypesAreCloseableTest extends \unittest\TestCase {
  private $fixture;
  private $scope;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new ArmTypesAreCloseable();
    $this->scope= new TypeDeclarationScope();
    $this->scope->declarations[0]= new ClassNode(MODIFIER_PUBLIC, [], new TypeName('Test'));
  }
  
  /**
   * Creates a new 
   *
   * @param   string[] types
   * @return  xp.compiler.ast.ArmNode
   */
  private function newArmNode($types) {
    $assignments= $variables= [];
    foreach ($types as $i => $name) {
      $var= new VariableNode('a'.$i);
      $type= new TypeName($name);
      $assign= new AssignmentNode([
        'variable'   => $var,
        'expression' => new InstanceCreationNode([
          'type'       => $type,
          'parameters' => [],
          'body'       => null
        ]),
        'op'         => '='
      ]);
      $assignments[]= $assign;
      $variables[]= $var;
      $this->scope->setType($var, $type);
    }
    return new ArmNode($assignments, $variables, []);
  }

  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.ArmNode field
   * @return  var
   */
  private function verify(ArmNode $field) {
    return $this->fixture->verify($field, $this->scope);
  }
  
  #[@test]
  public function textReaderIsCloseable() {
    $this->assertNull(
      $this->verify($this->newArmNode(['io.streams.TextReader']))
    );
  }

  #[@test]
  public function objectIsNotCloseable() {
    $this->assertEquals(
      ['A403', 'Type lang.Object for assignment #1 in ARM block is not closeable'],
      $this->verify($this->newArmNode(['lang.Object']))
    );
  }

  #[@test]
  public function oneIsNotCloseable() {
    $this->assertEquals(
      ['A403', 'Type lang.Object for assignment #2 in ARM block is not closeable'],
      $this->verify($this->newArmNode(['io.streams.TextReader', 'lang.Object']))
    );
  }
}