<?php namespace xp\compiler\types;

use xp\compiler\ast\ParseTree;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\InterfaceNode;
use xp\compiler\ast\EnumNode;

/**
 * Represents a declared type
 *
 * @test    xp://net.xp_lang.tests.types.TypeDeclarationTest
 */
class TypeDeclaration extends Types {
  protected $tree= null;
  protected $parent= null;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.ast.ParseTree tree
   * @param   xp.compiler.types.Types parent
   */
  public function __construct(ParseTree $tree, Types $parent= null) {
    $this->tree= $tree;
    $this->parent= $parent;
  }

  /**
   * Returns parent type
   *
   * @return  xp.compiler.types.Types
   */
  public function parent() {
    return $this->parent;
  }

  /**
   * Returns modifiers
   *
   * @return int
   */
  public function modifiers() {
    return $this->tree->declaration->modifiers;
  }

  /**
   * Returns name
   *
   * @return  string
   */
  public function name() {
    $n= $this->tree->declaration->name->name;
    if ($this->tree->package) {
      $n= $this->tree->package->name.'.'.$n;
    }
    return $n;
  }

  /**
   * Returns literal for use in code
   *
   * @return  string
   */
  public function literal() {
    return isset($this->tree->declaration->literal)
      ? $this->tree->declaration->literal 
      : $this->tree->declaration->name->name
    ;
  }

  /**
   * Returns literal for use in code
   *
   * @return  string
   */
  public function kind() {
    switch ($decl= $this->tree->declaration) {
      case $decl instanceof ClassNode: return parent::CLASS_KIND;
      case $decl instanceof InterfaceNode: return parent::INTERFACE_KIND;
      case $decl instanceof EnumNode: return parent::ENUM_KIND;
      default: return parent::UNKNOWN_KIND;
    }
  }

  /**
   * Checks whether a given type instance is a subclass of this class.
   *
   * @param   xp.compiler.types.Types
   * @return  bool
   */
  public function isSubclassOf(Types $t) {
    return $this->parent ? $this->parent->equals($t) || $this->parent->isSubclassOf($t): false;
  }

  /**
   * Returns whether this type is enumerable (that is: usable in foreach)
   *
   * @return  bool
   */
  public function isEnumerable() {
    // TBI
    return false;
  }

  /**
   * Returns the enumerator for this class or null if none exists.
   *
   * @see     php://language.oop5.iterations
   * @return  xp.compiler.types.Enumerator
   */
  public function getEnumerator() {
    // TBI
    return null;
  }

  /**
   * Returns whether a constructor exists
   *
   * @return  bool
   */
  public function hasConstructor() {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\ConstructorNode) return true;
    }
    return $this->parent ? $this->parent->hasConstructor() : false;
  }

  /**
   * Returns the constructor
   *
   * @return  xp.compiler.types.Constructor
   */
  public function getConstructor() {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\ConstructorNode) {
        $c= new Constructor();
        $c->modifiers= $member->modifiers;
        foreach ($member->parameters as $p) {
          $c->parameters[]= $p['type'];
        }
        $c->holder= $this;
        return $c;
      }
    }
    return $this->parent ? $this->parent->getConstructor() : null;
  }
  
  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasMethod($name) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\MethodNode && $member->name === $name) return true;
    }
    return $this->parent ? $this->parent->hasMethod($name) : false;
  }
  
  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Method
   */
  public function getMethod($name) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\MethodNode && $member->name === $name) {
        $m= new Method();
        $m->name= $member->name;
        $m->returns= $member->returns;
        $m->modifiers= $member->modifiers;
        foreach ((array)$member->parameters as $p) {
          $m->parameters[]= $p['type'];
        }
        $m->holder= $this;
        return $m;
      }
    }
    return $this->parent ? $this->parent->getMethod($name) : null;
  }

  /**
   * Gets a list of extension methods
   *
   * @return  [:xp.compiler.types.Method[]]
   */
  public function getExtensions() {
    $r= array();
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\MethodNode && $member->extension) {
        $n= $member->extension->compoundName();

        $m= new Method();
        $m->name= $member->name;
        $m->returns= $member->returns;
        $m->modifiers= $member->modifiers;
        foreach ((array)$member->parameters as $p) {
          $m->parameters[]= $p['type'];
        }
        $m->holder= $this;

        isset($r[$n]) || $r[$n]= array();
        $r[$n][]= $m;
      }
    }
    return $r;
  }

  /**
   * Returns whether an operator by a given symbol exists
   *
   * @param   string symbol
   * @return  bool
   */
  public function hasOperator($symbol) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\OperatorNode && $member->symbol === $symbol) return true;
    }
    return $this->parent ? $this->parent->hasOperator($symbol) : false;
  }
  
  /**
   * Returns an operator by a given name
   *
   * @param   string symbol
   * @return  xp.compiler.types.Operator
   */
  public function getOperator($symbol) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\OperatorNode && $member->symbol === $symbol) {
        $m= new Operator($member->symbol);
        $m->returns= $member->returns;
        $m->modifiers= $member->modifiers;
        foreach ($member->parameters as $p) {
          $m->parameters[]= $p->type;
        }
        $m->holder= $this;
        return $m;
      }
    }
    return $this->parent ? $this->parent->getOperator($symbol) : null;
  }

  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasField($name) {
    foreach ($this->tree->declaration->body as $member) {
      if (
        ($member instanceof \xp\compiler\ast\FieldNode && $member->name === $name) ||
        ($member instanceof \xp\compiler\ast\EnumMemberNode && $member->name === $name)
      ) return true;
    }
    return $this->parent ? $this->parent->hasField($name) : false;
  }
  
  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Field
   */
  public function getField($name) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\FieldNode && $member->name === $name) {
        $f= new Field();
        $f->name= $member->name;
        $f->modifiers= $member->modifiers;
        $f->type= $member->type;
        $f->holder= $this;
        return $f;
      } else if ($member instanceof \xp\compiler\ast\EnumMemberNode) {
        $f= new Field();
        $f->name= $member->name;
        $f->modifiers= $member->modifiers;
        $f->type= $this->tree->declaration->name;
        $f->holder= $this;
        return $f;
      }
    }
    return $this->parent ? $this->parent->getField($name) : null;
  }

  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasProperty($name) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\PropertyNode && $member->name === $name) return true;
    }
    return $this->parent ? $this->parent->hasProperty($name) : false;
  }
  
  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Property
   */
  public function getProperty($name) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\PropertyNode && $member->name === $name) {
        $p= new Property();
        $p->name= $member->name;
        $p->modifiers= $member->modifiers;
        $p->type= $member->type;
        $p->holder= $this;
        return $p;
      }
    }
    return $this->parent ? $this->parent->getProperty($name) : null;
  }

  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasConstant($name) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\ClassConstantNode && $member->name === $name) return true;
    }
    return $this->parent ? $this->parent->hasConstant($name) : false;
  }
  
  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Constant
   */
  public function getConstant($name) {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\ClassConstantNode && $member->name === $name) {
        $c= new Constant();
        $c->name= $member->name;
        $c->type= $member->type;
        $c->value= \cast($member->value, 'xp.compiler.ast.Resolveable')->resolve();
        $c->holder= $this;
        return $c;
      }
    }
    return $this->parent ? $this->parent->getConstant($name) : null;
  }

  /**
   * Returns whether this class has an indexer
   *
   * @return  bool
   */
  public function hasIndexer() {
    foreach ($this->tree->declaration->body as $member) {
      if ($member instanceof \xp\compiler\ast\IndexerNode) return true;
    }
    return $this->parent ? $this->parent->hasIndexer() : false;
  }

  /**
   * Returns indexer
   *
   * @return  xp.compiler.types.Indexer
   */
  public function getIndexer() {
    foreach ($this->tree->declaration->body as $member) {
      if (!$member instanceof \xp\compiler\ast\IndexerNode) continue;
      $i= new Indexer();
      $i->type= $member->type;
      $i->parameter= $member->parameter['type'];
      $i->holder= $this;
      return $i;
    }
    return $this->parent ? $this->parent->getIndexer() : null;
  }

  /**
   * Returns a lookup map of generic placeholders
   *
   * @return  [:int]
   */
  public function genericPlaceholders() {
    return array();
  }
  
  /**
   * Creates a string representation of this object
   *
   * @return  string
   */    
  public function toString() {
    return $this->getClassName().'@('.$this->tree->declaration->name->toString().')';
  }
}
