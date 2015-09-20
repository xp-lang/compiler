<?php namespace xp\compiler\emit\php;

use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\types\TypeName;

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
        defined(\'HHVM_VERSION\') && $iter->next();
        $send= eval(\'return function() use($iter) {
          $recv= null;
          $send= true;
          while ($iter->valid()) {
            $next= $iter->current();
            $send ? $iter->send($recv) : $iter->throw($recv);
            try {
              $recv= \'.(defined(\'HHVM_VERSION\') ? \'yield $next\' : \'(yield $next)\').\';
              $send= true;
            } catch (\\Exception $e) {
              $recv= $e;
              $send= false;
            }
          }
        };\');
        foreach ($send() as $next) { yield $next; }
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

  /**
   * Emit an automatic resource management (ARM) block
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArmNode arm
   */
  protected function emitArm($b, $arm) {
    $this->emitAll($b, $arm->initializations);

    // Manually verify as we can then rely on call target type being available
    if (!$this->checks->verify($arm, $this->scope[0], $this, true)) return;

    $exceptionType= $this->literal($this->resolveType(new TypeName('php.Exception')));
    $ignored= $this->tempVar();

    $b->append('try {');
    $this->emitAll($b, (array)$arm->statements);
    $b->append('} finally {');
    foreach ($arm->variables as $v) {
      $b->append('try { $')->append($v->name)->append('->close(); } ');
      $b->append('catch (')->append($exceptionType)->append(' ')->append($ignored)->append(') {}');
    }
    $b->append('}');
  }
}