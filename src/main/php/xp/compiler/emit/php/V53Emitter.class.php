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
    $name= $t->name();
    $package= substr($name, 0, strrpos($name, '.'));
    if ('' === $package || 0 === strncmp('php', $package, 3)) {
      return '\\'.$t->literal($base);
    } else {
      return '\\'.strtr($package, '.', '\\').'\\'.$t->literal($base);
    }
  }

  /**
   * Emit type name and modifiers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   string type
   * @param   xp.compiler.ast.TypeDeclarationNode declaration
   */
  protected function emitTypeName($b, $type, TypeDeclarationNode $declaration) {
    $this->metadata[0]['class']= array();
    if ($this->scope[0]->package) {
      $declaration->literal= strtr($this->scope[0]->package->name, '.', '\\').'\\'.$declaration->name->name;
    } else {
      $declaration->literal= $declaration->name->name;
    }
    
    // Emit abstract and final modifiers
    if (Modifiers::isAbstract($declaration->modifiers)) {
      $b->append('abstract ');
    } else if (Modifiers::isFinal($declaration->modifiers)) {
      $b->append('final ');
    } 
    
    // Emit declaration
    $b->append(' ')->append($type)->append(' ')->append($declaration->name->name);
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
