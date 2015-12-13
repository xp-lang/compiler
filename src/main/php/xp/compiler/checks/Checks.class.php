<?php namespace xp\compiler\checks;

/**
 * Compiler checks
 *
 * @see   xp://xp.compiler.checks.Check
 * @test  xp://net.xp_lang.tests.checks.ChecksTest
 */
class Checks extends \lang\Object {
  protected $impl= null;
  
  /**
   * Constructor.
   *
   */
  public function __construct() {
    $this->clear();
  }
  
  /**
   * Add a check
   *
   * @param   xp.compiler.checks.Check impl
   * @param   bool error
   */
  public function add(Check $impl, $error) {
    $this->impl[$impl->defer()][]= [$impl->node(), $impl, $error];
  }

  /**
   * Clear all implementations
   *
   */
  public function clear() {
    $this->impl= [false => [], true => []];
  }

  /**
   * Verify a given node
   *
   * @param   xp.compiler.ast.Node in
   * @param   xp.compiler.types.Scope scope
   * @param   var messages
   * @param   bool defer default false
   * @return  bool whether to continue or not
   */
  public function verify(\xp\compiler\ast\Node $in, \xp\compiler\types\Scope $scope, $messages, $defer= false) {
    $continue= true;
    foreach ($this->impl[$defer] as $impl) {
      if (!$impl[0]->isInstance($in)) continue;
      if (!($message= $impl[1]->verify($in, $scope))) continue;
      if ($impl[2]) {
        $messages->error($message[0], $message[1], $in);
        $continue= false;
      } else {
        $messages->warn($message[0], $message[1], $in);
      }
    }
    return $continue;
  }
}
