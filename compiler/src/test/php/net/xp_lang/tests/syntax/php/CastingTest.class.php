<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\CastNode;
use xp\compiler\ast\InvocationNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class CastingTest extends ParserTestCase {

  /**
   * Test a bool-cast
   *
   */
  #[@test]
  public function boolCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('bool'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (bool)1;'));
  }

  /**
   * Test a string-cast
   *
   */
  #[@test]
  public function stringCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('string'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (string)1;'));
  }

  /**
   * Test a array-cast
   *
   */
  #[@test]
  public function arrayCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('var[]'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (array)1;'));
  }

  /**
   * Test a int-cast
   *
   */
  #[@test]
  public function intCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('int'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (int)1;'));
  }

  /**
   * Test a double-cast
   *
   */
  #[@test]
  public function doubleCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('double'),
        'expression'    => new IntegerNode('1')
      )),
      'op'            => '=',
    ))), $this->parse('$a= (double)1;'));
  }


  /**
   * Test an invocation with a constants as argument is not confused with a cast
   *
   */
  #[@test]
  public function invocationWithConstantArg() {
    $this->assertEquals(
      array(new InvocationNode('init', array(new BooleanNode(true)))),
      $this->parse('init(true);')
    );
  }

  /**
   * Test a case-cast
   *
   */
  #[@test]
  public function castCast() {
    $this->assertEquals(array(new AssignmentNode(array(
      'variable'      => new VariableNode('a'),
      'expression'    => new CastNode(array(
        'type'          => new TypeName('bool'),
        'expression'    => new CastNode(array(
          'type'          => new TypeName('string'),
          'expression'    => new IntegerNode('1')
        )),
      )),
      'op'            => '=',
    ))), $this->parse('$a= (bool)(string)1;'));
  }
}
?>
