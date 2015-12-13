<?php namespace xp\compiler\ast;

/**
 * Promote all variables used inside a node to member variables except for
 * the ones passed in as excludes, returning all replacements.
 *
 * @test    xp://net.xp_lang.tests.LocalsToMemberPromoterTest
 */
class LocalsToMemberPromoter extends Visitor {
  protected $excludes= ['this' => true];
  protected $replacements= [];

  protected static $THIS;
  
  static function __static() {
    self::$THIS= new VariableNode('this');
  }

  /**
   * Visit a variable
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitVariable(VariableNode $node) {
    $n= $node->name;
    if (!isset($this->excludes[$n])) {
      $this->replacements['$'.$n]= $node= new MemberAccessNode(self::$THIS, $node->name);
    }
    return $node;
  }

  /**
   * Add a variable to exclude from promotion
   *
   * @param   string name
   */
  public function exclude($name) {
    $this->excludes[$name]= true;
  }

  /**
   * Run
   *
   * @param   xp.compiler.ast.Node nodes
   * @return  array<string, xp.compiler.ast.MemberAccessNode> replaced
   */
  public function promote($node) {
    $this->replacements= [];
    $node= $this->visitOne($node);
    return ['replaced' => $this->replacements, 'node' => $node];
  }
}
