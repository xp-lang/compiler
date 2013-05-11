<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\ArmTypesAreCloseable;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\ArmNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.checks.ArmTypesAreCloseable
 */
class ArmTypesAreCloseableTest extends \unittest\TestCase {
  protected $fixture= null;
  protected $scope= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= new ArmTypesAreCloseable();
    $this->scope= new TypeDeclarationScope();
    $this->scope->declarations[0]= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Test'));
  }
  
  /**
   * Creates a new 
   *
   * @param   string[] types
   * @return  xp.compiler.ast.ArmNode
   */
  protected function newArmNode($types) {
    $assignments= $variables= array();
    foreach ($types as $i => $name) {
      $var= new VariableNode('a'.$i);
      $type= new TypeName($name);
      $assign= new AssignmentNode(array(
        'variable'   => $var,
        'expression' => new InstanceCreationNode(array(
          'type'       => $type,
          'parameters' => array(),
          'body'       => null
        )),
        'op'         => '='
      ));
      $assignments[]= $assign;
      $variables[]= $var;
      $this->scope->setType($var, $type);
    }
    return new ArmNode($assignments, $variables, array());
  }

  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.ArmNode field
   * @return  var
   */
  protected function verify(ArmNode $field) {
    return $this->fixture->verify($field, $this->scope);
  }
  
  /**
   * Test io.streams.TextReader
   *
   */
  #[@test]
  public function textReaderIsCloseable() {
    $this->assertNull(
      $this->verify($this->newArmNode(array('io.streams.TextReader')))
    );
  }

  /**
   * Test lang.Object
   *
   */
  #[@test]
  public function objectIsNotCloseable() {
    $this->assertEquals(
      array('A403', 'Type lang.Object for assignment #1 in ARM block is not closeable'),
      $this->verify($this->newArmNode(array('lang.Object')))
    );
  }

  /**
   * Test io.streams.TextReader and lang.Object
   *
   */
  #[@test]
  public function oneIsNotCloseable() {
    $this->assertEquals(
      array('A403', 'Type lang.Object for assignment #2 in ARM block is not closeable'),
      $this->verify($this->newArmNode(array('io.streams.TextReader', 'lang.Object')))
    );
  }
}