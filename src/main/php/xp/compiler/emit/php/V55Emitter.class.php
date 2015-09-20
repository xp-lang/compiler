<?php namespace xp\compiler\emit\php;

use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\VariableNode;

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

  /**
   * Emit a try / catch block
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TryNode try
   */
  protected function emitTry($b, $try) {
    $b->append('try {');
    $this->emitAll($b, (array)$try->statements);
    $b->append('}');

    foreach ($try->handling as $handling) {
      if ($handling instanceof FinallyNode) {
        $b->append('finally {');
      } else {
        $b->append('catch('.$this->literal($this->resolveType($handling->type)).' $'.$handling->variable.') {');
        $this->scope[0]->setType(new VariableNode($handling->variable), $handling->type);
      }
      $this->emitAll($b, (array)$handling->statements);
      $b->append('}');
    }
  }
}