<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\StaticMemberAccessNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\types\TypeName;

/**
 * TestCase for assignments
 */
class AssignmentTest extends ParserTestCase {

  #[@test]
  public function toVariable() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new IntegerNode('0'),
      'op'            => '='
    ))), $this->parse('$i= 0;'));
  }

  #[@test]
  public function addAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new IntegerNode('1'),
      'op'            => '+='
    ))), $this->parse('$i += 1;'));
  }

  #[@test]
  public function subAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new IntegerNode('1'),
      'op'            => '-='
    ))), $this->parse('$i -= 1;'));
  }

  #[@test]
  public function mulAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new IntegerNode('2'),
      'op'            => '*='
    ))), $this->parse('$i *= 2;'));
  }

  /**
   * Test assigning to a variable via "/="
   *
   */
  #[@test]
  public function divAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new IntegerNode('2'),
      'op'            => '/='
    ))), $this->parse('$i /= 2;'));
  }

  #[@test]
  public function modAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new IntegerNode('2'),
      'op'            => '%='
    ))), $this->parse('$i %= 2;'));
  }

  #[@test]
  public function concatAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('s'),
      'expression'    => new StringNode('.'),
      'op'            => '~='
    ))), $this->parse('$s ~= ".";'));
  }

  #[@test]
  public function shiftRightAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('s'),
      'expression'    => new IntegerNode('2'),
      'op'            => '>>='
    ))), $this->parse('$s >>= 2;'));
  }

  #[@test]
  public function shiftLeftAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('s'),
      'expression'    => new IntegerNode('2'),
      'op'            => '<<='
    ))), $this->parse('$s <<= 2;'));
  }

  #[@test]
  public function orAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('s'),
      'expression'    => new IntegerNode('2'),
      'op'            => '|='
    ))), $this->parse('$s |= 2;'));
  }

  #[@test]
  public function andAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('s'),
      'expression'    => new IntegerNode('2'),
      'op'            => '&='
    ))), $this->parse('$s &= 2;'));
  }

  #[@test]
  public function xorAssign() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('s'),
      'expression'    => new IntegerNode('2'),
      'op'            => '^='
    ))), $this->parse('$s ^= 2;'));
  }

  #[@test]
  public function toArrayOffset() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new ArrayAccessNode(new VariableNode('i'), new IntegerNode('0')),
      'expression'    => new IntegerNode('0'),
      'op'            => '='
    ))), $this->parse('$i[0]= 0;'));
  }

  #[@test]
  public function appendToArray() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new ArrayAccessNode(new VariableNode('i'), null),
      'expression'    => new IntegerNode('0'),
      'op'            => '='
    ))), $this->parse('$i[]= 0;'));
  }

  #[@test]
  public function toInstanceMember() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new MemberAccessNode(new VariableNode('class'), 'member'),
      'expression'    => new IntegerNode('0'),
      'op'            => '='
    ))), $this->parse('$class.member= 0;'));
  }

  #[@test]
  public function toClassMember() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new StaticMemberAccessNode(new TypeName('self'), 'instance'),
      'expression'    => new NullNode(),
      'op'            => '='
    ))), $this->parse('self::$instance= null;'));
  }

  #[@test]
  public function toChain() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new MemberAccessNode(
        new MethodCallNode(
          new StaticMemberAccessNode(new TypeName('self'), 'instance'),
          'addAppender',
          null
        ),
        'flags'
      ),
      'expression'    => new IntegerNode('0'),
      'op'            => '='
    ))), $this->parse('self::$instance.addAppender().flags= 0;'));
  }

  #[@test]
  public function toAssignment() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new AssignmentNode(array(
        'variable'    => new VariableNode('j'),
        'expression'  => new IntegerNode('0'),
        'op'          => '='
      )),
      'op'            => '='
    ))), $this->parse('$i= $j= 0;'));
  }

  #[@test]
  public function toBracedAssignment() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('i'),
      'expression'    => new BracedExpressionNode(new AssignmentNode(array(
        'variable'    => new VariableNode('j'),
        'expression'  => new IntegerNode('0'),
        'op'          => '='
      ))),
      'op'            => '='
    ))), $this->parse('$i= ($j= 0);'));
  }
}
