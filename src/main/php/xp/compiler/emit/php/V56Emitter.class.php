<?php namespace xp\compiler\emit\php;

use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\types\TypeName;

/**
 * Emits sourcecode using PHP 5.6 sourcecode
 */
class V56Emitter extends V55Emitter {
  protected static $UNPACK_REWRITE = false;

  /**
   * Emit an unpack node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.UnpackNode unpack
   */
  protected function emitUnpack($b, $unpack) {
    $b->append('...');
    $this->emitOne($b, $unpack->expression);
  }
}