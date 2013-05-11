<?php namespace net\xp_lang\tests;

use xp\compiler\ast\StatementsNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\IntegerNode;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.ast.LocalsToMemberPromoter
 */
class LocalsToMemberPromoterTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Creates fixture
   *
   */
  public function setUp() {
    $this->fixture= new \xp\compiler\ast\LocalsToMemberPromoter();
  }
  
  /**
   * Creates a member node
   *
   * @param   string name
   * @return  xp.compiler.ast.MemberAccessNode
   */
  protected function memberNode($name) {
    return new MemberAccessNode(new VariableNode('this'), $name);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function basic() {
    $assignment= new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new IntegerNode('0'),
      'op'            => '='
    ));
    $promoted= $this->fixture->promote($assignment);
    $this->assertEquals(array('$i' => $this->memberNode('i')), $promoted['replaced']);
    $this->assertEquals(
      new AssignmentNode(array(
        'variable'      => $this->memberNode('i'),
        'expression'    => new IntegerNode('0'),
        'op'            => '='
      )),
      $promoted['node']
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function withExclusion() {
    $assignment= new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new VariableNode('a'),
      'op'            => '='
    ));
    $this->fixture->exclude('a');
    $promoted= $this->fixture->promote($assignment);
    $this->assertEquals(array('$i' => $this->memberNode('i')), $promoted['replaced']);
    $this->assertEquals(
      new AssignmentNode(array(
        'variable'      => $this->memberNode('i'),
        'expression'    => new VariableNode('a'),
        'op'            => '='
      )),
      $promoted['node']
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function memberNodesArentTouched() {
    $assignment= new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => $this->memberNode('i'),
      'op'            => '='
    ));
    $promoted= $this->fixture->promote($assignment);
    $this->assertEquals(array('$i' => $this->memberNode('i')), $promoted['replaced']);
    $this->assertEquals(
      new AssignmentNode(array(
        'variable'      => $this->memberNode('i'),
        'expression'    => $this->memberNode('i'),
        'op'            => '='
      )),
      $promoted['node']
    );
  }
}
