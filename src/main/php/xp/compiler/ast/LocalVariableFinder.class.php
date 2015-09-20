<?php namespace xp\compiler\ast;

/**
 * Finds local variables in a given block
 */
class LocalVariableFinder extends Visitor {
  protected $found= [];
  protected $excludes= ['this' => true];
  protected $includes= [];

  /**
   * Visit a variable
   *
   * @param   xp.compiler.ast.Node $node
   */
  protected function visitVariable(VariableNode $node) {
    if (isset($this->includes[$node->name])) {
      $this->found[$node->name]= true;
    } else if (!isset($this->excludes['*']) && !isset($this->excludes[$node->name])) {
      $this->found[$node->name]= true;
    }
    return $node;
  }

  /**
   * Do not recurse into lambdas!
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitLambda(LambdaNode $node) {
    return $node;
  }

  /**
   * Add a variable to exclude
   *
   * @param   string $name
   * @return  self
   */
  public function excluding($name) {
    $this->excludes[$name]= true;
    return $this;
  }

  /**
   * Add a variable to include
   *
   * @param   string $name
   * @return  self
   */
  public function including($name) {
    $this->includes[$name]= true;
    return $this;
  }

  /**
   * Run
   *
   * @param   xp.compiler.ast.Node[] $nodes The block
   * @return  string[] names
   */
  public function variablesIn(array $nodes) {
    $this->found= [];
    $node= $this->visitAll($nodes);
    return array_keys($this->found);
  }
}
