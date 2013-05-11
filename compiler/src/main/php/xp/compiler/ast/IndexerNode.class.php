<?php namespace xp\compiler\ast;

/**
 * Represents an indexer property
 *
 * ```php
 * class T {
 *   private string[] $elements = [];
 *
 *   public string this[int $offset] {
 *     get   { return $this.elements[$offset]; }
 *     set   { $this.elements[$offset]= $value; }
 *     isset { return isset($this.elements[$offset]); }
 *     unset { unset($this.elements[$offset]); }
 *   }
 * }
 * 
 * $t= new T();
 * $t[0]= 'Hello';            // Executes set-block
 * if (isset($t[0])) {        // Executes isset-block
 *   $hello= $t[0];           // Executes get-block
 *   unset($t[0]);            // Executes unset-block
 * }
 * ```
 *
 * @see   xp://xp.compiler.ast.PropertyNode
 */
class IndexerNode extends TypeMemberNode {
  public $type= null;
  public $parameter= null;
  public $handlers= array();
  
  /**
   * Returns this routine's name
   *
   * @return  string
   */
  public function getName() {
    return 'this';
  }

  /**
   * Returns this members's hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return 'this';
  }
}
