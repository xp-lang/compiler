<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\IfNode;
use xp\compiler\ast\ElseNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\SwitchNode;
use xp\compiler\ast\CaseNode;
use xp\compiler\ast\BreakNode;
use xp\compiler\ast\DefaultNode;
use xp\compiler\ast\StringNode;

/**
 * TestCase
 *
 */
class ControlStructuresTest extends ParserTestCase {

  /**
   * Test if statement without else
   *
   */
  #[@test]
  public function ifStatement() {
    $this->assertEquals(array(new IfNode(array(
      'condition'      => new VariableNode('i'),
      'statements'     => null,
      'otherwise'      => null, 
    ))), $this->parse('
      if ($i) { }
    '));
  }

  /**
   * Test if statement without curly braces
   *
   */
  #[@test]
  public function ifStatementWithOutCurlies() {
    $this->assertEquals(array(new IfNode(array(
      'condition'      => new VariableNode('i'),
      'statements'     => array(new ReturnNode(new BooleanNode(true))),
      'otherwise'      => null, 
    ))), $this->parse('
      if ($i) return true;
    '));
  }

  /**
   * Test if statement with else
   *
   */
  #[@test]
  public function ifElseStatement() {
    $this->assertEquals(array(new IfNode(array(
      'condition'      => new VariableNode('i'),
      'statements'     => null, 
      'otherwise'      => new ElseNode(array(
        'statements'     => null,
      )), 
    ))), $this->parse('
      if ($i) { } else { }
    '));
  }

  /**
   * Test if /else cascades
   *
   */
  #[@test]
  public function ifElseCascades() {
    $this->assertEquals(array(new IfNode(array(
      'condition'      => new BinaryOpNode(array(
        'lhs'            => new VariableNode('i'),
        'rhs'            => new IntegerNode('3'),
        'op'             => '%'
      )),
      'statements'     => null, 
      'otherwise'      => new ElseNode(array(
        'statements'     => array(new IfNode(array(
          'condition'      => new BinaryOpNode(array(
            'lhs'            => new VariableNode('i'),
            'rhs'            => new IntegerNode('2'),
            'op'             => '%'
          )),
          'statements'     => null, 
          'otherwise'      => new ElseNode(array(
            'statements'     => null,
          )), 
        ))),
      )), 
    ))), $this->parse('
      if ($i % 3) { } else if ($i % 2) { } else { }
    '));
  }

  /**
   * Test switch statement
   *
   */
  #[@test]
  public function emptySwitchStatement() {
    $this->assertEquals(array(new SwitchNode(array(
      'expression'     => new VariableNode('i'),
      'cases'          => null,
    ))), $this->parse('
      switch ($i) { }
    '));
  }

  /**
   * Test switch statement
   *
   */
  #[@test]
  public function switchStatement() {
    $this->assertEquals(array(new SwitchNode(array(
      'expression'     => new VariableNode('i'),
      'cases'          => array(
        new CaseNode(array(
          'expression'     => new IntegerNode('0'),
          'statements'     => array(
            new StringNode('no entries'),
            new BreakNode()
          )
        )),
        new CaseNode(array(
          'expression'     => new IntegerNode('1'),
          'statements'     => array(
            new StringNode('one entry'),
            new BreakNode()
          )
       )),
        new DefaultNode(array(
          'statements'     => array(
            new BinaryOpNode(array(
              'lhs'        => new VariableNode('i'),
              'rhs'        => new StringNode(' entries'),
              'op'         => '~'
            )),
            new BreakNode()
          )
        ))
      ),
    ))), $this->parse('
      switch ($i) { 
        case 0: "no entries"; break;
        case 1: "one entry"; break;
        default: $i." entries"; break;
      }
    '));
  }
}
