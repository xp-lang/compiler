<?php namespace net\xp_lang\tests\checks;

use xp\compiler\checks\ArrayAccessVerification;
use xp\compiler\types\MethodScope;
use xp\compiler\types\TypeName;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\MapNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\Node;

class ArrayAccessVerificationTest extends \unittest\TestCase {
  private $fixture;

  /**
   * Sets up test case
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new ArrayAccessVerification();
  }
  
  /**
   * Wrapper around verify
   *
   * @param   xp.compiler.ast.Node call
   * @return  var
   */
  private function verify(Node $target) {
    return $this->fixture->verify(new ArrayAccessNode($target, new IntegerNode(0)), new MethodScope());
  }
  
  #[@test]
  public function stringArray() {
    $this->assertNull(
      $this->verify(new ArrayNode(array('type' => new TypeName('string[]'), 'values' => array())))
    );
  }

  #[@test]
  public function stringMap() {
    $this->assertNull(
      $this->verify(new MapNode(array('type' => new TypeName('[:string]'), 'elements' => array())))
    );
  }

  #[@test]
  public function int() {
    $this->assertEquals(
      array('T305', 'Using array-access on unsupported type xp.compiler.types.TypeName(int)'),
      $this->verify(new IntegerNode())
    );
  }

  #[@test]
  public function undeclared() {
    $this->assertEquals(
      array('T203', 'Array access (var)[0] verification deferred until runtime'),
      $this->verify(new VariableNode('undeclared'))
    );
  }

  #[@test]
  public function string() {
    $this->assertNull(
      $this->verify(new StringNode())
    );
  }

  #[@test]
  public function arrayList() {
    $this->assertNull(
      $this->verify(new InstanceCreationNode(array('type' => new TypeName('lang.types.ArrayList'))))
    );
  }

  #[@test]
  public function anonymousIListInstance() {
    $this->assertNull(
      $this->verify(new InstanceCreationNode(array('type' => new TypeName('util.collections.IList'), 'body' => array(
        // Implementation missing, irrelevant to this test
      ))))
    );
  }

  #[@test]
  public function object() {
    $this->assertEquals(
      array('T305', 'Type lang.Object does not support offset access'),
      $this->verify(new InstanceCreationNode(array('type' => new TypeName('lang.Object'))))
    );
  }
}