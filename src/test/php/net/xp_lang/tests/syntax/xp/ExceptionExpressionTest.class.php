<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\TryNode;
use xp\compiler\ast\CatchNode;
use xp\compiler\ast\ThrowNode;
use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\ArmNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class ExceptionExpressionTest extends ParserTestCase {

  /**
   * Test try/catch
   *
   */
  #[@test]
  public function singleCatch() {
    $this->assertEquals(array(new TryNode(array(
      'statements' => array(new MethodCallNode(new VariableNode('method'), 'call')), 
      'handling'   => array(
        new CatchNode(array(
          'type'       => new TypeName('IllegalArgumentException'),
          'variable'   => 'e',
          'statements' => array(new MethodCallNode(new VariableNode('this'), 'finalize')), 
        ))
      )
    ))), $this->parse('
      try {
        $method.call();
      } catch (IllegalArgumentException $e) {
        $this.finalize();
      }
    '));
  }

  /**
   * Test try/finally
   *
   */
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

  /**
   * Test try/finally
   *
   */
  #[@test]
  public function singleFinally() {
    $this->assertEquals(array(new TryNode(array(
      'statements' => array(
        new ThrowNode(array(
          'expression' => new InstanceCreationNode(array(
            'type'       => new TypeName('ChainedException'),
            'parameters' => array(
              new StringNode('Hello'),
              new VariableNode('e'),
            )
          ))
        ))
      ), 
      'handling'   => array(
        new FinallyNode(array(
          'statements' => array(new MethodCallNode(new VariableNode('this'), 'finalize')), 
        ))
      )
    ))), $this->parse('
      try {
        throw new ChainedException("Hello", $e);
      } finally {
        $this.finalize();
      }
    '));
  }

  /**
   * Test try w/ multiple catches
   *
   */
  #[@test]
  public function multipleCatches() {
    $this->assertEquals(array(new TryNode(array(
      'statements' => array(
        new ReturnNode(new InstanceCreationNode(array(
          'type'       => new TypeName('util.collections.HashTable', array(
            new TypeName('string'), 
            new TypeName('lang.Object')
          )),
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
        return new util.collections.HashTable<string, lang.Object>();
      } catch (IllegalArgumentException $e) {
      } catch (SecurityException $e) {
        throw $e;
      } catch (Exception $e) {
      }
    '));
  }

  /**
   * Test try w/ multi catch
   *
   */
  #[@test]
  public function multiCatch() {
    $this->assertEquals(array(new TryNode(array(
      'statements' => array(
        new ReturnNode(new InstanceCreationNode(array(
          'type'       => new TypeName('util.collections.HashTable', array(
            new TypeName('string'), 
            new TypeName('lang.Object')
          )),
          'parameters' => null
        )))
      ), 
      'handling'   => array(
        new CatchNode(array(
          'type'       => new TypeName('IllegalArgumentException'),
          'variable'   => 'e',
          'statements' => array(new ThrowNode(array(
            'expression' => new VariableNode('e')
          ))), 
        )),
        new CatchNode(array(
          'type'       => new TypeName('SecurityException'),
          'variable'   => 'e',
          'statements' => array(new ThrowNode(array(
            'expression' => new VariableNode('e')
          ))), 
        ))
      )
    ))), $this->parse('
      try {
        return new util.collections.HashTable<string, lang.Object>();
      } catch (IllegalArgumentException | SecurityException $e) {
        throw $e;
      }
    '));
  }

  /**
   * Test ARM statement
   *
   */
  #[@test]
  public function resourceManagementWithAssignment() {
    $this->assertEquals(array(new ArmNode(
      array(new AssignmentNode(array(
        'variable'   => new VariableNode('r'),
        'expression' => new InstanceCreationNode(array(
          'type'       => new TypeName('TextReader'),
          'parameters' => array(new VariableNode('stream')),
          'body'       => null
        )),
        'op'         => '='
      ))),
      array(new VariableNode('r')),
      array(new ReturnNode(new MethodCallNode(new VariableNode('r'), 'readLine')))
    )), $this->parse('
      try ($r= new TextReader($stream)) {
        return $r.readLine();
      }
    '));
  }

  /**
   * Test ARM statement
   *
   */
  #[@test]
  public function resourceManagement() {
    $this->assertEquals(array(
      new ArmNode(
        array(),
        array(new VariableNode('r')),
        array(new ReturnNode(new MethodCallNode(new VariableNode('r'), 'readLine')))       
      )
    ), $this->parse('
      try ($r) {
        return $r.readLine();
      }
    '));
  }

  /**
   * Test ARM statement
   *
   */
  #[@test]
  public function resourceManagementWithTwoVariables() {
    $this->assertEquals(array(
      new ArmNode(
        array(),
        array(new VariableNode('in'), new VariableNode('out')),
        array(new ReturnNode(new MethodCallNode(new VariableNode('out'), 'write', array(
          new MethodCallNode(new VariableNode('in'), 'read')
        ))))       
      )
    ), $this->parse('
      try ($in, $out) {
        return $out.write($in.read());
      }
    '));
  }
}
