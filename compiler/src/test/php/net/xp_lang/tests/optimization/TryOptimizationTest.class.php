<?php namespace net\xp_lang\tests\optimization;

use xp\compiler\optimize\Optimizations;
use xp\compiler\optimize\TryOptimization;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\TryNode;
use xp\compiler\ast\CatchNode;
use xp\compiler\ast\ThrowNode;
use xp\compiler\ast\NoopNode;
use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\StatementsNode;
use xp\compiler\types\TypeName;
use xp\compiler\types\MethodScope;

/**
 * TestCase for Try operations
 *
 * @see      xp://xp.compiler.optimize.TryOptimization
 */
class TryOptimizationTest extends \unittest\TestCase {
  protected $fixture = null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new Optimizations();
    $this->fixture->add(new TryOptimization());
    $this->scope= new MethodScope();
  }
  
  /**
   * Test try { ... } catch (... $e) { throw $e; } is optimized to
   * just the statements inside the try block
   */
  #[@test]
  public function removeUselessTryCatch() {
    $try= new TryNode(array(
      'statements' => array(new ReturnNode(new NullNode())),
      'handling'   => array(
        new CatchNode(array(
          'type'       => new TypeName('lang.Throwable'),
          'variable'   => 'e',
          'statements' => array(new ThrowNode(array('expression' => new VariableNode('e'))))
        ))
      )
    ));

    $this->assertEquals(
      new StatementsNode($try->statements), 
      $this->fixture->optimize($try, $this->scope)
    );
  }

  /**
   * Test try { } catch (... $e) { ... } is optimized to a NOOP
   */
  #[@test]
  public function emptyTryBecomesNoop() {
    $try= new TryNode(array(
      'statements' => array(),
      'handling'   => array(
        new CatchNode(array(
          'type'       => new TypeName('lang.Throwable'),
          'variable'   => 'e',
          'statements' => array(new ReturnNode( new NullNode()))
        ))
      )
    ));

    $this->assertEquals(
      new NoopNode(), 
      $this->fixture->optimize($try, $this->scope)
    );
  }

  /**
   * Test try { } finally { ... } is not optimized to the statements
   * inside the finally block.
   */
  #[@test]
  public function emptyTryWithFinally() {
    $try= new TryNode(array(
      'statements' => array(),
      'handling'   => array(
        new FinallyNode(array(
          'statements' => array(new ReturnNode(new NullNode()))
        ))
      )
    ));

    $this->assertEquals(
      new StatementsNode($try->handling[0]->statements), 
      $this->fixture->optimize($try, $this->scope)
    );
  }
}
