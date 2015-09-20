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
    $this->assertEquals(array(new TryNode(array(
      'statements' => array(new MethodCallNode(new VariableNode('method'), 'call')),
      'handling'   => array(
        new CatchNode(array(
          'type'       => new TypeName('IllegalArgumentException'),
          'variable'   => 'e',
          'statements' => array(new MethodCallNode(new VariableNode('this'), 'finalize'))
        ))
      )
    ))), $this->parse('
      try {
        $method->call();
      } catch (IllegalArgumentException $e) {
        $this->finalize();
      }
    '));
  }

  #[@test]
  public function singleThrow() {
    $this->assertEquals(array(new ThrowNode(array(
      'expression' => new InstanceCreationNode(array(
        'type'       => new TypeName('IllegalStateException'),
        'parameters' => null
      ))
    ))), $this->parse('
      throw new IllegalStateException();
    '));
  }

  #[@test]
  public function multipleCatches() {
    $this->assertEquals(array(new TryNode(array(
      'statements' => array(
        new ReturnNode(new InstanceCreationNode(array(
          'type'       => new TypeName('HashTable'),
          'parameters' => null
        )))
      ), 
      'handling'   => array(
        new CatchNode(array(
          'type'       => new TypeName('IllegalArgumentException'),
          'variable'   => 'e',
          'statements' => null, 
        )),
        new CatchNode(array(
          'type'       => new TypeName('SecurityException'),
          'variable'   => 'e',
          'statements' => array(new ThrowNode(array(
            'expression' => new VariableNode('e')
          ))), 
        )),
        new CatchNode(array(
          'type'       => new TypeName('Exception'),
          'variable'   => 'e',
          'statements' => null, 
        ))
      )
    ))), $this->parse('
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
