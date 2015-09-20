<?php namespace xp\compiler\emit\php;

use xp\compiler\types\CompiledType;
use xp\compiler\types\TypeDeclaration;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\TypeDeclarationNode;
use xp\compiler\ast\Resolveable;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\ConstructorNode;
use xp\compiler\ast\IndexerNode;
use xp\compiler\ast\StaticInitializerNode;
use xp\compiler\ast\CatchNode;
use xp\compiler\ast\FinallyNode;
use xp\compiler\emit\Buffer;
use lang\reflect\Modifiers;

/**
* Emits sourcecode using PHP 5.4 sourcecode
 */
class V54Emitter extends Emitter {
  protected $core= '\\xp';

  /**
   * Returns the literal for a given type
   *
   * @param  xp.compiler.types.Types t
   * @param  bool base Whether to use only the base type
   * @return string
   */
  protected function literal($t, $base= false) {
    $package= $t->package();
    if ('' === $package || 0 === strncmp('php', $package, 3)) {
      return '\\'.$t->literal($base);
    } else {
      return '\\'.strtr($package, '.', '\\').'\\'.$t->literal($base);
    }
  }

  /**
   * Returns the literal for a given declaration
   *
   * @param  xp.compiler.ast.TypeDeclarationNode decl
   * @param  bool package whether to include the package or not
   * @return string
   */
  protected function declaration($decl, $package= true) {
    if ($package && $this->scope[0]->package) {
      return strtr($this->scope[0]->package->name, '.', '\\').'\\'.$decl->name->name;
    } else {
      return $decl->name->name;
    }
  }

  /**
   * Emit uses statements for a given list of types
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   [:bool] types
   */
  protected function emitUses($b, array $types) {
    if ($this->scope[0]->package) {
      $b->insert('namespace '.strtr($this->scope[0]->package->name, '.', '\\').';', 0);
      $import= $b->mark();
    } else {
      $import= 0;
    }

    // Emit import statements for classes providing extension methods
    foreach ($types as $name => $loaded) {
      $ptr= $this->resolveType(new TypeName($name));
      if ($ptr->getExtensions()) {
        $b->insert('new \\import(\''.$ptr->name().'\');', $import);
      }
    }
  }

  /**
   * Emit a try / catch block
   * 
   * Simple form:
   * <code>
   *   try {
   *     // [...statements...]
   *   } catch (lang.Throwable $e) {
   *     // [...error handling...]
   *   }
   * </code>
   *
   * Multiple catches:
   * <code>
   *   try {
   *     // [...statements...]
   *   } catch (lang.IllegalArgumentException $e) {
   *     // [...error handling for IAE...]
   *   } catch (lang.FormatException $e) {
   *     // [...error handling for FE...]
   *   }
   * </code>
   *
   * Try/finally without catch:
   * <code>
   *   try {
   *     // [...statements...]
   *   } finally {
   *     // [...finalizations...]
   *   }
   * </code>
   *
   * Try/finally with catch:
   * <code>
   *   try {
   *     // [...statements...]
   *   } catch (lang.Throwable $e) {
   *     // [...error handling...]
   *   } finally {
   *     // [...finalizations...]
   *   }
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TryNode try
   */
  protected function emitTry($b, $try) {
    static $mangled= '··e';

    // Check whether a finalization handler is available. If so, because
    // the underlying runtime does not support this, add statements after
    // the try block and to all catch blocks
    $numHandlers= sizeof($try->handling);
    if ($try->handling[$numHandlers- 1] instanceof FinallyNode) {
      array_unshift($this->finalizers, array_pop($try->handling));
      $numHandlers--;
    } else {
      array_unshift($this->finalizers, null);
    }

    // If no handlers are left, create a simple catch-all-and-rethrow
    // handler
    if (0 == $numHandlers) {
      $rethrow= new ThrowNode(array('expression' => new VariableNode($mangled)));
      $first= new CatchNode(array(
        'type'       => new TypeName('lang.Throwable'),
        'variable'   => $mangled,
        'statements' => $this->finalizers[0] ? array($this->finalizers[0], $rethrow) : array($rethrow)
      ));
    } else {
      $first= $try->handling[0];
      $this->scope[0]->setType(new VariableNode($first->variable), $first->type);
    }

    $b->append('try {'); {
      $this->emitAll($b, (array)$try->statements);
      $this->finalizers[0] && $this->emitOne($b, $this->finalizers[0]);
    }

    // First catch.
    $b->append('} catch('.$this->literal($this->resolveType($first->type)).' $'.$first->variable.') {'); {
      $this->scope[0]->setType(new VariableNode($first->variable), $first->type);
      $this->emitAll($b, (array)$first->statements);
      $this->finalizers[0] && $this->emitOne($b, $this->finalizers[0]);
    }

    // Additional catches
    for ($i= 1; $i < $numHandlers; $i++) {
      $b->append('} catch('.$this->literal($this->resolveType($try->handling[$i]->type)).' $'.$try->handling[$i]->variable.') {'); {
        $this->scope[0]->setType(new VariableNode($try->handling[$i]->variable), $try->handling[$i]->type);
        $this->emitAll($b, (array)$try->handling[$i]->statements);
        $this->finalizers[0] && $this->emitOne($b, $this->finalizers[0]);
      }
    }

    $b->append('}');
    array_shift($this->finalizers);
  }

  /**
   * Emit an automatic resource management (ARM) block
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArmNode arm
   */
  protected function emitArm($b, $arm) {
    static $mangled= '··e';
    static $ignored= '··i';

    $this->emitAll($b, $arm->initializations);

    // Manually verify as we can then rely on call target type being available
    if (!$this->checks->verify($arm, $this->scope[0], $this, true)) return;

    $exceptionType= $this->literal($this->resolveType(new TypeName('php.Exception')));
    $b->append('$'.$mangled.'= NULL; try {');
    $this->emitAll($b, (array)$arm->statements);
    $b->append('} catch (')->append($exceptionType)->append('$'.$mangled.') {}');
    foreach ($arm->variables as $v) {
      $b->append('try { $')->append($v->name)->append('->close(); } ');
      $b->append('catch (')->append($exceptionType)->append(' $'.$ignored.') {}');
    }
    $b->append('if ($'.$mangled.') throw $'.$mangled.';'); 
  }

  /**
   * Emit a lambda
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.LambdaNode lambda
   * @see     http://de3.php.net/manual/de/functions.anonymous.php
   */
  protected function emitLambda($b, $lambda) {

    // Parameters
    $b->append('function(');
    $s= sizeof($lambda->parameters)- 1;
    foreach ($lambda->parameters as $i => $param) {
      $b->append('$')->append($param['name']);
      if (isset($param['default'])) {
        $b->append('=');
        $this->emitOne($b, $param['default']);
      }
      $i < $s && $b->append(',');
    }
    $b->append(')');

    // If not explicitely stated: Capture all local variables and parameters of
    // containing scope which are also used inside the lambda by value.
    if (null === $lambda->uses) {
      $finder= new \xp\compiler\ast\LocalVariableFinder();
      foreach ($finder->variablesIn((array)$this->scope[0]->routine->body) as $variable) {
        $finder->including($variable);
      }
      foreach ($this->scope[0]->routine->parameters as $param) {
        $finder->including($param['name']);
      }
      $finder->excluding('*');

      // Use variables
      if ($capture= $finder->variablesIn($lambda->statements)) {
        $b->append(' use($')->append(implode(', $', $capture))->append(')');
      }
    } else if ($lambda->uses) {
      $capture= array_map(function($var) { return $var['name']; }, $lambda->uses);
      $b->append(' use($')->append(implode(', $', $capture))->append(')');
    }

    $b->append('{');
    $this->emitAll($b, $lambda->statements);
    $b->append('}');
  }
}
