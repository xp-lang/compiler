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
    return $t->literal($base);
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

    // Check whether class needs to be fully qualified
    if ($declaration->modifiers & MODIFIER_PACKAGE) {
      $b->append('$package= \'')->append($this->scope[0]->package->name)->append("';");
      $declaration->literal= strtr($this->scope[0]->package->name, '.', '·').'·'.$declaration->name->name;
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
    $b->append(' ')->append($type)->append(' ')->append($declaration->literal);
  }

  /**
   * Emits class registration
   *
   * <code>
   *   xp::$cn['class.'.$name]= $qualified;
   *   xp::$meta['details.'.$qualified]= $meta;
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TypeDeclarationNode
   * @param   string qualified
   */
  protected function registerClass($b, $declaration, $qualified) {
    unset($this->metadata[0]['EXT']);

    // Retain comment
    $this->metadata[0]['class'][DETAIL_COMMENT]= $declaration->comment
      ? trim(preg_replace('/\n\s+\* ?/', "\n", "\n ".substr($declaration->comment, 4, strpos($declaration->comment, '* @')- 2)))
      : null
    ;

    // Copy annotations
    $this->emitAnnotations($this->metadata[0]['class'], (array)$declaration->annotations);

    $b->append('xp::$cn[\''.$declaration->literal.'\']= \''.$qualified.'\';');
    $b->append('xp::$meta[\''.$qualified.'\']= '.var_export($this->metadata[0], true).';');
    
    // Run static initializer if existant on synthetic types
    if ($declaration->synthetic && $this->inits[0][2]) {
      $b->append($declaration->literal)->append('::__static();');
    }
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
