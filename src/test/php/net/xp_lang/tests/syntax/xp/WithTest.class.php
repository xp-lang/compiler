<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\WithNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\types\TypeName;

/**
 * With statement
 */
class WithTest extends ParserTestCase {

  /**
   * Test statement with one assignment
   *
   */
  #[@test]
  public function oneAssignment() {
    $this->assertEquals(
      [new WithNode(
        [new AssignmentNode([
          'variable'   => new VariableNode('o'),
          'op'         => '=',
          'expression' => new InstanceCreationNode([
            'type'       => new TypeName('Object'),
            'parameters' => [],
          ])
        ])],
        []
      )], 
      $this->parse('with ($o= new Object()) { }')
    );
  }

  /**
   * Test statement with two assignments
   *
   */
  #[@test]
  public function twoAssignments() {
    $this->assertEquals(
      [new WithNode(
        [new AssignmentNode([
          'variable'   => new VariableNode('o1'),
          'op'         => '=',
          'expression' => new InstanceCreationNode([
            'type'       => new TypeName('Object'),
            'parameters' => [],
          ])
        ]), new AssignmentNode([
          'variable'   => new VariableNode('o2'),
          'op'         => '=',
          'expression' => new InstanceCreationNode([
            'type'       => new TypeName('Object'),
            'parameters' => [],
          ])
        ])],
        []
      )], 
      $this->parse('with ($o1= new Object(), $o2= new Object()) { }')
    );
  }

  /**
   * Test statement with block statements
   *
   */
  #[@test]
  public function statements() {
    $this->assertEquals(
      [new WithNode(
        [new AssignmentNode([
          'variable'   => new VariableNode('o'),
          'op'         => '=',
          'expression' => new InstanceCreationNode([
            'type'       => new TypeName('Object'),
            'parameters' => [],
          ])
        ])],
        [new AssignmentNode([
          'variable'   => new VariableNode('s'),
          'op'         => '=',
          'expression' => new MethodCallNode(new VariableNode('o'), 'toString', null)
        ])]
      )], 
      $this->parse('with ($o= new Object()) { $s= $o.toString(); }')
    );
  }
}
