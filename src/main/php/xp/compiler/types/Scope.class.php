<?php namespace xp\compiler\types;

use util\collections\HashTable;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\MapNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\NaturalNode;
use xp\compiler\ast\DecimalNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\ast\ComparisonNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\Node;
use lang\XPClass;
use lang\ClassLoader;

/**
 * Represents the current scope
 *
 * @test    xp://tests.types.ScopeTest
 */
abstract class Scope extends \lang\Object {
  protected $task= null;
  protected $types= null;
  protected $extensions= [];
  protected $resolved= [];
  protected $packages= ['lang'];
  protected $enclosing= null;

  public $importer= null;
  public $declarations= [];
  public $imports= [];
  public $used= [];
  public $package= null;
  public $statics= [];

  /**
   * Constructor
   *
   */
  public function __construct() {
    $this->types= create('new util.collections.HashTable<xp.compiler.ast.Node, xp.compiler.types.TypeName>()');
    $this->resolved= create('new util.collections.HashTable<string, xp.compiler.types.Types>()');
  }
  
  /**
   * Enter a child scope
   *
   * @param   xp.compiler.types.Scope child
   * @return  xp.compiler.types.Scope child
   */
  public function enter(self $child) {
    $child->enclosing= $this;
    
    // Copy everything except types which are per-scope
    $child->importer= $this->importer;
    $child->task= $this->task;
    $child->package= $this->package;
    $child->resolved= $this->resolved;
    
    // Reference arrays - TODO: Refactor and use Vectors instead
    $child->extensions= &$this->extensions;
    $child->declarations= &$this->declarations;
    $child->imports= &$this->imports;
    $child->packages= &$this->packages;
    $child->used= &$this->used;
    $child->statics= &$this->statics;

    return $child;
  }

  /**
   * Add a type to resolved
   *
   * @param   string type
   * @param   xp.compiler.types.Types resolved
   */
  public function addResolved($type, Types $resolved) {
    $this->resolved[$type]= $resolved;
  }

  /**
   * Gets list of resolved types
   *
   * @return  util.collections.HashTable<string, xp.compiler.types.Types>
   */
  public function resolvedTypes() {
    return $this->resolved;
  }
  
  /**
   * Add a type import
   *
   * @param   string import fully qualified class name
   * @throws  xp.compiler.types.ResolveException
   */
  public function addTypeImport($import) {
    $p= strrpos($import, '.');
    $this->imports[substr($import, $p+ 1)]= $import;
    $ptr= $this->resolveType(new TypeName($import));

    // Register extension methods ([:xp.compiler.types.Method[]])
    foreach ($ptr->getExtensions() as $type => $methods) {
      foreach ($methods as $method) {
        $this->addExtension($this->resolveType(new TypeName($type)), $method);
      }
    }
  }

  /**
   * Add an extension method
   *
   * @param   xp.compiler.types.Types type
   * @param   xp.compiler.types.Method method
   */
  public function addExtension(Types $type, Method $method) {
    $this->extensions[$type->name().$method->name]= $method;
  }

  /**
   * Add a package import
   *
   * @param   string import fully qualified package name
   * @throws  xp.compiler.types.ResolveException
   */
  public function addPackageImport($import) {
    try {
      $this->packages[]= $this->task->locatePackage($import);
    } catch (\lang\ElementNotFoundException $e) {
      throw new ResolveException('Cannot import non-existant package '.$import, 507, $e);
    }
  }
  
  /**
   * Get an extension method
   *
   * @param   xp.compiler.types.Types type
   * @param   string name method name
   * @return  xp.compiler.types.Method
   */
  public function getExtension(Types $type, $name) {

    // Check parent chain
    do {
      $k= $type->name().$name;
      if (isset($this->extensions[$k])) return $this->extensions[$k];
    } while ($type= $type->parent());
    
    // Nothing found
    return null;
  }

  /**
   * Resolve a static call. Return true if the target is a function
   * (e.g. key()), a xp.compiler.types.Method instance if it's a static 
   * method (Map::key()).
   *
   * @param   string name
   * @return  var
   */
  public function resolveStatic($name) {
    foreach ($this->statics[0] as $lookup => $type) {
      if (true === $type && $this->importer->hasFunction($lookup, $name)) {
        return true;
      } else if ($type instanceof Types && $type->hasMethod($name)) {
        $m= $type->getMethod($name);
        if (\lang\reflect\Modifiers::isStatic($m->modifiers)) return $m;
      }
    }
    return null;
  }

  /**
   * Resolve a static call. Return true if the target is a function
   * (e.g. key()), a xp.compiler.types.Constant instance if it's a static 
   * method (HttpConstants::STATUS_OK).
   *
   * @param   string name
   * @return  xp.compiler.types.Constant
   */
  public function resolveConstant($name) {
    foreach ($this->statics[0] as $lookup => $type) {
      if ($type instanceof Types && $type->hasConstant($name)) {
        return $type->getConstant($name);
      }
    }
    return null;
  }
  
  /**
   * Resolve a type name
   *
   * @param   xp.compiler.types.TypeName name
   * @param   bool register
   * @return  xp.compiler.types.Types resolved
   * @throws  xp.compiler.types.ResolveException
   */
  public function resolveType(TypeName $name, $register= true) {
    if ($name->isArray()) {
      return new ArrayTypeOf($this->resolveType($name->arrayComponentType(), $register));
    } else if ($name->isMap()) {
      return new MapTypeOf($this->resolveType($name->mapComponentType(), $register));
    } else if (!$name->isClass()) {
      return new PrimitiveTypeOf($name);
    } else if ($name->isGeneric()) {
      return new GenericType($this->resolveType(new TypeName($name->name), $register), $name->components);
    } else if ($name->isFunction()) {
      return new FunctionTypeOf($this->resolveType($name->functionReturnType(), $register), $name->components);
    }

    if ($this->declarations) {
      $decl= $this->declarations[0];

      // Keywords: self, parent
      if ('self' === $name->name || $name->name === $decl->name->name) {
        return $this->resolved['self'];
      } else if ('parent' === $name->name) {
        return $this->resolved['parent'];
      }

      // See if this type is part of our generic type, return a place holder
      foreach ($decl->name->components as $component) {
        if ($component->equals($name)) return new TypeReference($name, Types::UNKNOWN_KIND);
      }
      
      // Fall through
    }

    $normalized= strtr($name->name, '\\', '.');
    if ('xp' === $normalized) {
      return new TypeReference($name, Types::UNKNOWN_KIND);
    } else if (0 === strncmp('php.', $normalized, 4)) {
      return new TypeReflection(new XPClass(substr($normalized, strrpos($normalized, '.')+ 1)));
    } else if (strpos($normalized, '.')) {
      $qualified= $normalized;
    } else if (isset($this->imports[$normalized])) {
      $qualified= $this->imports[$normalized];
    } else {
      $lookup= $this->package
        ? array_merge($this->packages, [$this->package->name])
        : array_merge($this->packages, [null])
      ;
      try {
        $qualified= $this->task->locateClass($lookup, $normalized);
      } catch (\lang\ElementNotFoundException $e) {
        throw new ResolveException('Cannot resolve '.$name->toString(), 423, $e);
      }
    }
    
    
    // Locate class. If the classloader already knows this class,
    // we can simply use this class. TODO: Use specialized 
    // JitClassLoader?
    if (!$this->resolved->containsKey($qualified)) {
      if (
        class_exists(literal($qualified), false) || 
        interface_exists(literal($qualified), false) || 
        ClassLoader::getDefault()->providesClass($qualified)
      ) {
        try {
          $this->resolved[$qualified]= new TypeReflection(XPClass::forName($qualified));
        } catch (\lang\Throwable $e) {
          throw new ResolveException('Class loader error for '.$name->toString().': '.$e->getMessage(), 507, $e);
        }
      } else {
        try {
          $type= $this->task->newSubTask($qualified)->run($this);
        } catch (\xp\compiler\CompilationException $e) {
          throw new ResolveException('Cannot resolve '.$name->toString(), 424, $e);
        } catch (\lang\Throwable $e) {
          throw new ResolveException('Cannot resolve '.$name->toString(), 507, $e);
        }
        $this->resolved[$qualified]= $type;
      }
    }

    $register && $this->used[$qualified]= true;
    return $this->resolved[$qualified];
  }
  
  /**
   * Set type
   *
   * @param   xp.compiler.ast.Node node
   * @param   xp.compiler.types.TypeName type
   */
  public function setType(Node $node, TypeName $type) {
    $this->types->put($node, $type);
  }

  /**
   * Get type
   *
   * @param   xp.compiler.ast.Node node
   * @return  xp.compiler.types.TypeName type or null
   */
  public function getType(Node $node) {
    return $this->types->get($node);
  }
  
  /**
   * Return a type for a given node
   *
   * @param   xp.compiler.ast.Node node
   * @return  xp.compiler.types.TypeName
   */
  public function typeOf(Node $node) {
    if ($node instanceof ArrayNode) {
      return $node->type ?: new TypeName('var[]');
    } else if ($node instanceof MapNode) {
      return $node->type ?: new TypeName('[:var]');
    } else if ($node instanceof StringNode) {
      return new TypeName('string');
    } else if ($node instanceof NaturalNode) {
      return new TypeName('int');
    } else if ($node instanceof DecimalNode) {
      return new TypeName('double');
    } else if ($node instanceof NullNode) {
      return new TypeName('lang.Object');
    } else if ($node instanceof BooleanNode) {
      return new TypeName('bool');
    } else if ($node instanceof ComparisonNode) {
      return new TypeName('bool');
    } else if ($node instanceof AssignmentNode) {
      return $this->typeOf($node->variable);
    } else if ($node instanceof InstanceCreationNode) {
      return $node->type;
    } else if ($node instanceof BracedExpressionNode) {
      return $this->typeOf($node->expression);
    } else if ($this->types->containsKey($node)) {
      return $this->types[$node];
    }
    return TypeName::$VAR;
  }
}
