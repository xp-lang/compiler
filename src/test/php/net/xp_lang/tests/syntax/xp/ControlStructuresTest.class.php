<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\IfNode;
use xp\compiler\ast\ElseNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\SwitchNode;
use xp\compiler\ast\CaseNode;
use xp\compiler\ast\DefaultNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\BreakNode;

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
    $this->assertEquals([new IfNode([
      'condition'      => new VariableNode('i'),
      'statements'     => null,
      'otherwise'      => null, 
    ])], $this->parse('
      if ($i) { }
    '));
  }

  /**
   * Test if statement without curly braces
   *
   */
  #[@test]
  public function ifStatementWithOutCurlies() {
    $this->assertEquals([new IfNode([
      'condition'      => new VariableNode('i'),
      'statements'     => [new ReturnNode(new BooleanNode(true))],
      'otherwise'      => null, 
    ])], $this->parse('
      if ($i) return true;
    '));
  }

  /**
   * Test if statement with else
   *
   */
  #[@test]
  public function ifElseStatement() {
    $this->assertEquals([new IfNode([
      'condition'      => new VariableNode('i'),
      'statements'     => null, 
      'otherwise'      => new ElseNode([
        'statements'     => null,
      ]), 
    ])], $this->parse('
      if ($i) { } else { }
    '));
  }

  /**
   * Test if /else cascades
   *
   */
  #[@test]
  public function ifElseCascades() {
    $this->assertEquals([new IfNode([
      'condition'      => new BinaryOpNode([
        'lhs'            => new VariableNode('i'),
        'rhs'            => new IntegerNode('3'),
        'op'             => '%'
      ]),
      'statements'     => null, 
      'otherwise'      => new ElseNode([
        'statements'     => [new IfNode([
          'condition'      => new BinaryOpNode([
            'lhs'            => new VariableNode('i'),
            'rhs'            => new IntegerNode('2'),
            'op'             => '%'
          ]),
          'statements'     => null, 
          'otherwise'      => new ElseNode([
            'statements'     => null,
          ]), 
        ])],
      ]), 
    ])], $this->parse('
      if ($i % 3) { } else if ($i % 2) { } else { }
    '));
  }

  /**
   * Test switch statement
   *
   */
  #[@test]
  public function emptySwitchStatement() {
    $this->assertEquals([new SwitchNode([
      'expression'     => new VariableNode('i'),
      'cases'          => null,
    ])], $this->parse('
      switch ($i) { }
    '));
  }

  /**
   * Test switch statement
   *
   */
  #[@test]
  public function switchStatement() {
    $this->assertEquals([new SwitchNode([
      'expression'     => new VariableNode('i'),
      'cases'          => [
        new CaseNode([
          'expression'     => new IntegerNode('0'),
          'statements'     => [
            new StringNode('no entries'),
            new BreakNode()
          ]
        ]),
        new CaseNode([
          'expression'     => new IntegerNode('1'),
          'statements'     => [
            new StringNode('one entry'),
            new BreakNode()
          ]
       ]),
        new DefaultNode([
          'statements'     => [
            new BinaryOpNode([
              'lhs'        => new VariableNode('i'),
              'rhs'        => new StringNode(' entries'),
              'op'         => '~'
            ]),
            new BreakNode()
          ]
        ])
      ],
    ])], $this->parse('
      switch ($i) { 
        case 0: "no entries"; break;
        case 1: "one entry"; break;
        default: $i ~ " entries"; break;
      }
    '));
  }
}
