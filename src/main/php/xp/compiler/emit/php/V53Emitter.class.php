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
use xp\compiler\emit\Buffer;
use lang\reflect\Modifiers;

/**
* Emits sourcecode using PHP 5.3 sourcecode
 */
class V53Emitter extends Emitter {
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
}
