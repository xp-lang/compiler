<?php namespace xp\compiler\emit\php;

/**
* Emits sourcecode using PHP 5.5 sourcecode
 */
class V55Emitter extends V54Emitter {

  /**
   * Emit a yield node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.YieldNode yield
   */
  protected function emitYield($b, $yield) {
    $b->append('yield ');
    if ($yield->key) {
      $this->emitOne($b, $yield->key);
      $b->append(' => ');
      $this->emitOne($b, $yield->value);
    } else if ($yield->value) {
      $this->emitOne($b, $yield->value);
    }
  }
}