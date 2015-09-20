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
    $this->assertEquals(array(new ForNode(array(
      'initialization' => array(new AssignmentNode(array(
        'variable'       => new VariableNode('i'),
        'expression'     => new IntegerNode('0'),
        'op'             => '='
      ))),
      'condition'      => array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('1000'),
        'op'            => '<'
      ))),
      'loop'           => array(new UnaryOpNode(array(
        'expression'    => new VariableNode('i'),
        'op'            => '++',
        'postfix'       => true
      ))),
      'statements'     => null, 
    ))), $this->parse('
      for ($i= 0; $i < 1000; $i++) { }
    '));
  }

  #[@test]
  public function foreachLoop() {
    $this->assertEquals(array(new ForeachNode(array(
      'expression'    => new VariableNode('list'),
      'assignment'    => array('value' => 'value'),
      'statements'    => null, 
    ))), $this->parse('
      foreach ($list as $value) { }
    '));
  }

  #[@test]
  public function foreachLoopWithKey() {
    $this->assertEquals(array(new ForeachNode(array(
      'expression'    => new VariableNode('list'),
      'assignment'    => array('key' => 'key', 'value' => 'value'),
      'statements'    => null, 
    ))), $this->parse('
      foreach ($list as $key => $value) { }
    '));
  }

  #[@test]
  public function whileLoop() {
    $this->assertEquals(array(new WhileNode(
      new ComparisonNode(array(
        'lhs'           => new UnaryOpNode(array(
          'expression'    => new VariableNode('i'),
          'op'            => '++',
          'postfix'       => true
        )),
        'rhs'           => new IntegerNode('10000'),
        'op'            => '<'
      )),
      array(new UnaryOpNode(array(
        'expression'    => new VariableNode('i'),
        'op'            => '++',
        'postfix'       => true
      )))
    )), $this->parse('
      while ($i++ < 10000) { $i++; }
    '));
  }

  #[@test]
  public function doLoop() {
    $this->assertEquals(array(new DoNode(
      new ComparisonNode(array(
        'lhs'           => new UnaryOpNode(array(
          'expression'    => new VariableNode('i'),
          'op'            => '++',
          'postfix'       => true
        )),
        'rhs'           => new IntegerNode('10000'),
        'op'            => '<'
      )),
      array(new UnaryOpNode(array(
        'expression'    => new VariableNode('i'),
        'op'            => '++',
        'postfix'       => true
      )))
    )), $this->parse('
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

