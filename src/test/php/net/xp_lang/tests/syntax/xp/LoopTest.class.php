<?php namespace net\xp_lang\tests\syntax\xp;

use lang\FormatException;
use xp\compiler\ast\ForNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\ComparisonNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\ForeachNode;
use xp\compiler\ast\WhileNode;
use xp\compiler\ast\DoNode;

/**
 * TestCase
 *
 */
class LoopTest extends ParserTestCase {

  /**
   * Test for loop
   *
   */
  #[@test]
  public function forLoop() {
    $this->assertEquals([new ForNode([
      'initialization' => [new AssignmentNode([
        'variable'       => new VariableNode('i'),
        'expression'     => new IntegerNode('0'),
        'op'             => '='
      ])],
      'condition'      => [new ComparisonNode([
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('1000'),
        'op'            => '<'
      ])],
      'loop'           => [new UnaryOpNode([
        'expression'    => new VariableNode('i'),
        'op'            => '++',
        'postfix'       => true
      ])],
      'statements'     => null, 
    ])], $this->parse('
      for ($i= 0; $i < 1000; $i++) { }
    '));
  }

  /**
   * Test foreach loop
   *
   */
  #[@test]
  public function foreachLoop() {
    $this->assertEquals([new ForeachNode([
      'expression'    => new VariableNode('list'),
      'assignment'    => ['value' => 'value'],
      'statements'    => null, 
    ])], $this->parse('
      foreach ($value in $list) { }
    '));
  }

  /**
   * Test foreach loop
   *
   */
  #[@test]
  public function foreachLoopWithKey() {
    $this->assertEquals([new ForeachNode([
      'expression'    => new VariableNode('list'),
      'assignment'    => ['key' => 'key', 'value' => 'value'],
      'statements'    => null, 
    ])], $this->parse('
      foreach ($key, $value in $list) { }
    '));
  }

  /**
   * Test while loop
   *
   */
  #[@test]
  public function whileLoop() {
    $this->assertEquals([new WhileNode(
      new ComparisonNode([
        'lhs'           => new UnaryOpNode([
          'expression'    => new VariableNode('i'),
          'op'            => '++',
          'postfix'       => true
        ]),
        'rhs'           => new IntegerNode('10000'),
        'op'            => '<'
      ]),
      [new UnaryOpNode([
        'expression'    => new VariableNode('i'),
        'op'            => '++',
        'postfix'       => true
      ])]
    )], $this->parse('
      while ($i++ < 10000) { $i++; }
    '));
  }

  /**
   * Test do...while loop
   *
   */
  #[@test]
  public function doLoop() {
    $this->assertEquals([new DoNode(
      new ComparisonNode([
        'lhs'           => new UnaryOpNode([
          'expression'    => new VariableNode('i'),
          'op'            => '++',
          'postfix'       => true
        ]),
        'rhs'           => new IntegerNode('10000'),
        'op'            => '<'
      ]),
      [new UnaryOpNode([
        'expression'    => new VariableNode('i'),
        'op'            => '++',
        'postfix'       => true
      ])]
    )], $this->parse('
      do { $i++; } while ($i++ < 10000);
    '));
  }


  /**
   * Test while
   *
   */
  #[@test, @expect(FormatException::class)]
  public function whileLoopWithoutBody() {
    $this->parse('$a= 0; while ($a--);');
  }

  /**
   * Test foreach
   *
   */
  #[@test, @expect(FormatException::class)]
  public function foreachLoopWithoutBody() {
    $this->parse('foreach ($a in [1]);');
  }

  /**
   * Test while
   *
   */
  #[@test, @expect(FormatException::class)]
  public function forLoopWithoutBody() {
    $this->parse('for ($i= 0; $i < 1; $i++);');
  }
}
