<?php namespace xp\compiler\emit\php;

use xp\compiler\types\CompiledType;
use xp\compiler\types\TypeDeclaration;
use xp\compiler\types\TypeInstance;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use xp\compiler\types\Scope;
use xp\compiler\types\CompilationUnitScope;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\MethodScope;
use xp\compiler\types\Method;
use xp\compiler\types\Field;
use xp\compiler\types\Constructor;
use xp\compiler\types\Property;
use xp\compiler\types\Operator;
use xp\compiler\types\Indexer;
use xp\compiler\types\Constant;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\TypeDeclarationNode;
use xp\compiler\ast\Resolveable;
use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\StaticMemberAccessNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\CatchNode;
use xp\compiler\ast\ThrowNode;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\ConstructorNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\EnumMemberNode;
use xp\compiler\ast\IndexerNode;
use xp\compiler\ast\StaticInitializerNode;
use xp\compiler\ast\LocalsToMemberPromoter;
use xp\compiler\emit\Buffer;
use lang\reflect\Modifiers;
use lang\Throwable;

/**
 * Emits sourcecode using PHP 5.2 sourcecode
 */
class V52Emitter extends Emitter {

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

  /**
   * Emit method parameters
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   array<string, *>[] parameters
   * @param   string delim
   * @return  xp.compiler.TypeName[] the signature
   */
  protected function emitParameters($b, array $parameters, $delim) {
    $signature= array();
    $b->append('(');
    $s= sizeof($parameters)- 1;
    $defer= array();
    $usesGenerics= false;
    $genericParams= '';
    foreach ($parameters as $i => $param) {
      if (isset($param['assign'])) {
        if (null === ($field= $this->resolveType(new TypeName('self'))->getField($param['assign']))) {
          $this->error('F404', 'Method assignment parameter $this.'.$param['assign'].' references non-existant field');
          $t= TypeName::$VAR;
        } else {
          $t= $field->type;
        }
        $ptr= $this->resolveType($t);
        $param['name']= $param['assign'];
        $defer[]= '$this->'.$param['assign'].'= $'.$param['assign'].';';
      } else if (!$param['type']) {
        $t= TypeName::$VAR;
        $ptr= new TypeReference($t);
      } else {
        if (!$usesGenerics && $this->scope[0]->declarations[0]->name->isPlaceHolder($param['type'])) $usesGenerics= true;
        $t= $param['type'];
        $ptr= $this->resolveType($t);
        if (!$param['check'] || isset($param['vararg'])) {
          // No runtime type checks
        } else if ($t->isArray() || $t->isMap()) {
          $b->append('array ');
        } else if ($t->isClass() && !$this->scope[0]->declarations[0]->name->isPlaceHolder($t)) {
          $b->append($ptr->literal())->append(' ');
        } else if ('{' === $delim) {
          $defer[]= create(new Buffer('', $b->line))
            ->append('if (NULL !== $')->append($param['name'])->append(' && !is("'.$t->name.'", $')
            ->append($param['name'])
            ->append(')) throw new IllegalArgumentException("Argument ')
            ->append($i + 1)
            ->append(' passed to ".__METHOD__." must be of ')
            ->append($t->name)
            ->append(', ".xp::typeOf($')
            ->append($param['name'])
            ->append(')." given");')
          ;
        } else {
          // No checks in interfaces
        }
      }

      $signature[]= new TypeName($ptr->name());
      $genericParams.= ', '.$t->compoundName();
      $this->metadata[0][1][$this->method[0]][DETAIL_ARGUMENTS][$i]= $ptr->name();
      
      if (isset($param['vararg'])) {
        $genericParams.= '...';
        if ($i > 0) {
          $defer[]= '$'.$param['name'].'= array_slice(func_get_args(), '.$i.');';
        } else {
          $defer[]= '$'.$param['name'].'= func_get_args();';
        }
        $this->scope[0]->setType(new VariableNode($param['name']), new TypeName($t->name.'[]'));
        break;
      }
      
      $b->append('$'.$param['name']);
      if (isset($param['default'])) {
        $b->append('= ');
        $resolveable= false; 
        if ($param['default'] instanceof Resolveable) {
          try {
            $init= $param['default']->resolve();
            $b->append(var_export($init, true));
            $resolveable= true; 
          } catch (\lang\IllegalStateException $e) {
          }
        }
        if (!$resolveable) {
          $b->append('NULL');
          $init= new Buffer('', $b->line);
          $init->append('if (func_num_args() < ')->append($i + 1)->append(') { ');
          $init->append('$')->append($param['name'])->append('= ');
          $this->emitOne($init, $param['default']);
          $init->append('; }');
          $defer[]= $init;
        }
      }
      $i < $s && !isset($parameters[$i+ 1]['vararg']) && $b->append(',');
      
      $this->scope[0]->setType(new VariableNode($param['name']), $t);
    }
    $b->append(')');
    $b->append($delim);
    
    foreach ($defer as $src) {
      $b->append($src);
    }

    if ($usesGenerics) {
      $this->metadata[0][1][$this->method[0]][DETAIL_ANNOTATIONS]['generic']['params']= substr($genericParams, 2);
    }
    
    return $signature;
  }

  /**
   * Emit a class declaration
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ClassNode declaration
   */
  protected function emitClass($b, $declaration) {
    $parent= $declaration->parent ?: new TypeName('lang.Object');
    $parentType= $this->resolveType($parent);
    $thisType= new TypeDeclaration(new ParseTree($this->scope[0]->package, array(), $declaration), $parentType);
    $this->scope[0]->addResolved('self', $thisType);
    $this->scope[0]->addResolved('parent', $parentType);
    
    $this->enter(new TypeDeclarationScope());    
    $this->emitTypeName($b, 'class', $declaration);
    $b->append(' extends '.$parentType->literal(true));
    array_unshift($this->metadata, array(array(), array()));
    $this->metadata[0]['class'][DETAIL_ANNOTATIONS]= array();
    array_unshift($this->properties, array());
    array_unshift($this->inits, array(false => array(), true => array(), 2 => false));

    // Generics
    if ($declaration->name->isGeneric()) {
      $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['self']= $this->genericComponentAsMetadata($declaration->name);
    }
    if ($parent->isGeneric()) {
      $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['parent']= $this->genericComponentAsMetadata($parent);
    }

    // Check if we need to implement ArrayAccess
    foreach ((array)$declaration->body as $node) {
      if ($node instanceof IndexerNode) {
        $declaration->implements[]= 'ArrayAccess';
      }
    }
    
    // Interfaces
    if ($declaration->implements) {
      $b->append(' implements ');
      $s= sizeof($declaration->implements)- 1;
      foreach ($declaration->implements as $i => $type) {
        if ($type instanceof TypeName) {
          if ($type->isGeneric()) {
            $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['implements'][$i]= $this->genericComponentAsMetadata($type);
          }
          $b->append($this->resolveType($type)->literal(true));
        } else {
          $b->append($type);
        }
        $i < $s && $b->append(', ');
      }
    }
    
    // Members
    $b->append('{');
    foreach ((array)$declaration->body as $node) {
      $this->emitOne($b, $node);
    }
    $this->emitProperties($b, $this->properties[0]);
    
    // Generate a constructor if initializations are available.
    // They will have already been emitted if a constructor exists!
    if ($this->inits[0][false]) {
      $arguments= array();
      $parameters= array();
      if ($parentType->hasConstructor()) {
        foreach ($parentType->getConstructor()->parameters as $i => $type) {
          $parameters[]= array('name' => '··a'.$i, 'type' => $type);    // TODO: default
          $arguments[]= new VariableNode('··a'.$i);
        }
        $body= array(new StaticMethodCallNode(new TypeName('parent'), '__construct', $arguments));
      } else {
        $body= array();
      }
      $this->emitOne($b, new ConstructorNode(array(
        'modifiers'    => MODIFIER_PUBLIC,
        'parameters'   => $parameters,
        'annotations'  => null,
        'body'         => $body,
        'comment'      => '(Generated)',
        'position'     => $declaration->position
      )));
    }

    // Generate a static initializer if initializations are available.
    // They will have already been emitted if a static initializer exists!
    if ($this->inits[0][true]) {
      $this->emitOne($b, new StaticInitializerNode(null));
    }
    
    // Create __import
    if (isset($this->metadata[0]['EXT'])) {
      $b->append('static function __import($scope) {');
      foreach ($this->metadata[0]['EXT'] as $method => $type) {
        $b->append('xp::$ext[$scope]["')->append($type)->append('"]= "')->append($thisType->literal())->append('";');
      }
      $b->append('}');
    }

    // Generic instances have {definition-type, null, [argument-type[0..n]]} 
    // stored  as type names in their details
    if (isset($declaration->generic)) {
      $this->metadata[0]['class'][DETAIL_GENERIC]= $declaration->generic;
    }

    $b->append('}');
    $this->leave();
    $this->registerClass($b, $declaration, $thisType->name());
    array_shift($this->properties);
    array_shift($this->metadata);
    array_shift($this->inits);

    // Register type info
    $this->types[0]->name= $thisType->name();
    $this->types[0]->kind= Types::CLASS_KIND;
    $this->types[0]->literal= $declaration->literal;
    $this->types[0]->parent= $parentType;
  }
}
