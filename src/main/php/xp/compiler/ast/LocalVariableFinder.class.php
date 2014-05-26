<?php namespace xp\compiler\ast;

/**
 * Finds local variables in a given block
 */
class LocalVariableFinder extends Visitor {
  protected $found= array();
  protected $excludes= array('this' => true);

  /**
   * Visit a variable
   *
   * @param   xp.compiler.ast.Node $node
   */
  protected function visitVariable(VariableNode $node) {
    if (!isset($this->excludes[$node->name])) {
      $this->found[$node->name]= true;
    }
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
   * Run
   *
   * @param   xp.compiler.ast.Node[] $nodes The block
   * @return  string[] names
   */
  public function variablesIn(array $nodes) {
    $this->found= array();
    $node= $this->visitAll($nodes);
    return array_keys($this->found);
  }
}
