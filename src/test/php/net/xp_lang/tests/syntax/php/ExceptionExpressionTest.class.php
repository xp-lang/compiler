<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\TryNode;
use xp\compiler\ast\CatchNode;
use xp\compiler\ast\ThrowNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\types\TypeName;

class ExceptionExpressionTest extends ParserTestCase {

  #[@test]
  public function singleCatch() {
    $this->assertEquals([new TryNode([
      'statements' => [new MethodCallNode(new VariableNode('method'), 'call')],
      'handling'   => [
        new CatchNode([
          'type'       => new TypeName('IllegalArgumentException'),
          'variable'   => 'e',
          'statements' => [new MethodCallNode(new VariableNode('this'), 'finalize')]
        ])
      ]
    ])], $this->parse('
      try {
        $method->call();
      } catch (IllegalArgumentException $e) {
        $this->finalize();
      }
    '));
  }

  #[@test]
  public function singleThrow() {
    $this->assertEquals([new ThrowNode([
      'expression' => new InstanceCreationNode([
        'type'       => new TypeName('IllegalStateException'),
        'parameters' => null
      ])
    ])], $this->parse('
      throw new IllegalStateException();
    '));
  }

  #[@test]
  public function multipleCatches() {
    $this->assertEquals([new TryNode([
      'statements' => [
        new ReturnNode(new InstanceCreationNode([
          'type'       => new TypeName('HashTable'),
          'parameters' => null
        ]))
      ], 
      'handling'   => [
        new CatchNode([
          'type'       => new TypeName('IllegalArgumentException'),
          'variable'   => 'e',
          'statements' => null, 
        ]),
        new CatchNode([
          'type'       => new TypeName('SecurityException'),
          'variable'   => 'e',
          'statements' => [new ThrowNode([
            'expression' => new VariableNode('e')
          ])], 
        ]),
        new CatchNode([
          'type'       => new TypeName('Exception'),
          'variable'   => 'e',
          'statements' => null, 
        ])
      ]
    ])], $this->parse('
      try {
        return new HashTable();
      } catch (IllegalArgumentException $e) {
      } catch (SecurityException $e) {
        throw $e;
      } catch (Exception $e) {
      }
    '));
  }
}
