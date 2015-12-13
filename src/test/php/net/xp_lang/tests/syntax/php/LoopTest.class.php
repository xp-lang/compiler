<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\ForNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\ComparisonNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\ForeachNode;
use xp\compiler\ast\WhileNode;
use xp\compiler\ast\DoNode;

class LoopTest extends ParserTestCase {

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

  #[@test]
  public function foreachLoop() {
    $this->assertEquals([new ForeachNode([
      'expression'    => new VariableNode('list'),
      'assignment'    => ['value' => 'value'],
      'statements'    => null, 
    ])], $this->parse('
      foreach ($list as $value) { }
    '));
  }

  #[@test]
  public function foreachLoopWithKey() {
    $this->assertEquals([new ForeachNode([
      'expression'    => new VariableNode('list'),
      'assignment'    => ['key' => 'key', 'value' => 'value'],
      'statements'    => null, 
    ])], $this->parse('
      foreach ($list as $key => $value) { }
    '));
  }

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

  #[@test]
  public function whileLoopWithoutBody() {
    $this->parse('$a= 0; while ($a--);');
  }

  #[@test]
  public function foreachLoopWithoutBody() {
    $this->parse('foreach (array(1) as $v);');
  }

  #[@test]
  public function forLoopWithoutBody() {
    $this->parse('for ($i= 0; $i < 1; $i++);');
  }
}

