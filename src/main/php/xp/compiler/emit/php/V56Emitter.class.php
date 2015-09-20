<?php namespace xp\compiler\emit\php;

use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\types\TypeName;

/**
 * Emits sourcecode using PHP 5.6 sourcecode
 */
class V56Emitter extends V55Emitter {
  protected static $UNPACK_REWRITE = false;

}