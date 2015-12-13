<?php namespace xp\compiler\ast;

/**
 * Represents a property
 *
 * ```
 * class T {
 *   private int $_length= 0;
 *
 *   public int length {
 *     get { return $this._length; }
 *     set { $this._length= $value; }
 *   }
 * }
 * 
 * $t= new T();
 * $length= $t.length;    // Executes get-block
 * $t.length= 1;          // Executes set-block
 * ```
 *
 * @see   xp://xp.compiler.ast.IndexerNode
 */
class PropertyNode extends TypeMemberNode {
  public $type     = null;
  public $handlers = [];

  /**
   * Returns this members's hashcode
   *
   * @return  string
   */
  public function hashCode() {
    return '$'.$this->getName();
  }
}
