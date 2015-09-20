<?php namespace xp\compiler\emit\php;

/**
* Emits sourcecode using PHP 7.0 sourcecode
 */
class V70Emitter extends V55Emitter {

  /**
   * Emit a yield from node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.YieldFromNode yield
   */
  protected function emitYieldFrom($b, $yield) {
    $b->append('yield from ');
    $this->emitOne($b, $yield->expr);
  }
}