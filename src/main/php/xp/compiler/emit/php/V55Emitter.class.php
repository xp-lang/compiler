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

  /**
   * Emit a yield from node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.YieldFromNode yield
   */
  protected function emitYieldFrom($b, $yield) {
    static $shim= '
      if ($iter instanceof \\Generator) {
        $recv= null;
        $send= true;
        while ($iter->valid()) {
          $next= $iter->current();
          $send ? $iter->send($recv) : $iter->throw($recv);
          try {
            $recv= (yield $next);
            $send= true;
          } catch (\\Exception $e) {
            $recv= $e;
            $send= false;
          }
        }
      } else {
        foreach ($iter as $next) { yield $next; }
      }
    ';

    $iter= $this->tempVar();
    $b->append($iter)->append('=');
    $this->emitOne($b, $yield->expr);
    $b->append(';');

    $b->append(strtr($shim, [
      "\n"    => '',
      '  '    => '',
      '$iter' => $iter,
      '$recv' => $this->tempVar(),
      '$send' => $this->tempVar(),
      '$next' => $this->tempVar()
    ]));
  }
}