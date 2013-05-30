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
 * Emits sourcecode using PHP 5.2 sourcecode
 */
class V52Emitter extends Emitter {
  protected $core= 'xp';

  /**
   * Returns the literal for a given type
   *
   * @param  xp.compiler.types.Types t
   * @param  bool base Whether to use only the base type
   * @return string
   */
  protected function literal($t, $base= false) {
    if ($t->modifiers() & MODIFIER_PACKAGE) {
      return strtr($t->package(), '.', '·').'·'.$t->literal($base);
    } else {
      return $t->literal($base);
    }
  }

  /**
   * Returns the literal for a given declaration
   *
   * @param  xp.compiler.ast.TypeDeclarationNode decl
   * @return string
   */
  protected function declaration($decl) {
    if ($decl->modifiers & MODIFIER_PACKAGE) {
      return strtr($this->scope[0]->package->name, '.', '·').'·'.$decl->name->name;
    } else {
      return $decl->name->name;
    }
  }

  /**
   * Emit type name and modifiers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   string type
   * @param   xp.compiler.ast.TypeDeclarationNode declaration
   */
  protected function emitTypeName($b, $type, TypeDeclarationNode $declaration, $prefix= '') {

    // Check whether class needs to be fully qualified
    if ($declaration->modifiers & MODIFIER_PACKAGE) {
      $b->append('$package= \'')->append($this->scope[0]->package->name)->append("';");
      $prefix= strtr($this->scope[0]->package->name, '.', '·').'·';
    }

    return parent::emitTypeName($b, $type, $declaration, $prefix);
  }

  /**
   * Emit uses statements for a given list of types
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   [:bool] types
   */
  protected function emitUses($b, array $types) {
    static $bootstrap= array(
      'lang.Object' => true,
      'lang.StackTraceElement' => true,
      'lang.Throwable' => true,
      'lang.Error' => true,
      'lang.XPException' => true,
      'lang.Type' => true,
      'lang.Primitive' => true,
      'lang.types.Character' => true,
      'lang.types.Number' => true,
      'lang.types.Byte' => true,
      'lang.types.Bytes' => true,
      'lang.types.String' => true,
      'lang.types.Integer' => true,
      'lang.types.Double' => true,
      'lang.types.Boolean' => true,
      'lang.types.ArrayListIterator' => true,
      'lang.types.ArrayList' => true,
      'lang.ArrayType' => true,
      'lang.MapType' => true,
      'lang.reflect.Routine' => true,
      'lang.reflect.Parameter' => true,
      'lang.reflect.TargetInvocationException' => true,
      'lang.reflect.Method' => true,
      'lang.reflect.Field' => true,
      'lang.reflect.Constructor' => true,
      'lang.reflect.Modifiers' => true,
      'lang.reflect.Package' => true,
      'lang.XPClass' => true,
      'lang.NullPointerException' => true,
      'lang.IllegalAccessException' => true,
      'lang.IllegalArgumentException' => true,
      'lang.IllegalStateException' => true,
      'lang.FormatException' => true,
      'lang.ClassNotFoundException' => true,
      'lang.AbstractClassLoader' => true,
      'lang.FileSystemClassLoader' => true,
      'lang.DynamicClassLoader' => true,
      'lang.archive.ArchiveClassLoader' => true,
      'lang.ClassLoader' => true,
    );

    // Do not add uses() entries for:
    // * Types emitted inside the same sourcefile
    // * Native classes
    // * Bootstrap classes
    $this->cat && $this->cat->debug('uses(', $types, ')');
    $uses= array();
    foreach ($types as $type => $used) {
      if (isset($this->local[0][$type]) ||  'php.' === substr($type, 0, 4) ||  isset($bootstrap[$type])) continue;

      // TODO: Find out why this would make a difference, $type should already be fully-qualified
      // @net.xp_lang.tests.execution.source.PropertiesOverloadingTest
      // @net.xp_lang.tests.integration.CircularDependencyTest
      try {
        $uses[]= $this->resolveType(new TypeName($type), false)->name();
      } catch (Throwable $e) {
        $this->error('0424', $e->toString());
      }
    }
    $uses && $b->insert('uses(\''.implode("', '", $uses).'\');', 0);
  }
}
