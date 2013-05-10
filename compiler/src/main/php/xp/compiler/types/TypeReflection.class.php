<?php namespace xp\compiler\types;

use lang\XPClass;
use lang\Type;

/**
 * Represents a reflected type
 *
 * @test    xp://net.xp_lang.tests.tests.types.TypeReflectionTest
 */
class TypeReflection extends Types {
  protected $class= null;
  
  /**
   * Constructor
   *
   * @param   lang.XPClass class
   */
  public function __construct(XPClass $class) {
    $this->class= $class;
  }

  /**
   * Returns parent type
   *
   * @return  xp.compiler.types.Types
   */
  public function parent() {
    if ($parent= $this->class->getParentClass()) {
      return new self($parent);
    }
    return null;
  }
  
  /**
   * Returns name
   *
   * @return  string
   */
  public function name() {
    return $this->class->getName();
  }

  /**
   * Returns literal for use in code
   *
   * @return  string
   */
  public function literal() {
    return \xp::reflect($this->class->getName());
  }
  
  /**
   * Returns literal for use in code
   *
   * @return  string
   */
  public function kind() {
    if ($this->class->isInterface()) {
      return parent::INTERFACE_KIND;
    } else if ($this->class->isEnum()) {
      return parent::ENUM_KIND;
    } else {
      return parent::CLASS_KIND;
    }
  }

  /**
   * Checks whether a given type instance is a subclass of this class.
   *
   * @param   xp.compiler.types.Types
   * @return  bool
   */
  public function isSubclassOf(Types $t) {
    try {
      return $this->class->isSubclassOf($t->name());
    } catch (\lang\ClassNotFoundException $e) {
      return false;
    }
  }

  /**
   * Returns whether this type is enumerable (that is: usable in foreach)
   *
   * @see     php://language.oop5.iterations
   * @return  bool
   */
  public function isEnumerable() {
    return (
      $this->class->_reflect->implementsInterface('Iterator') || 
      $this->class->_reflect->implementsInterface('IteratorAggregate')
    );
  }

  /**
   * Returns the enumerator for this class or null if none exists.
   *
   * @see     php://language.oop5.iterations
   * @return  xp.compiler.types.Enumerator
   */
  public function getEnumerator() {
    if ($this->class->_reflect->implementsInterface('Iterator')) {
      $e= new Enumerator();
      $e->key= new TypeName($it->getMethod('key')->getReturnTypeName());
      $e->value= new TypeName($it->getMethod('current')->getReturnTypeName());
      $e->holder= $this;  
      return $e;
    } else if ($this->class->_reflect->implementsInterface('IteratorAggregate')) {
      $it= $this->class->getMethod('getIterator')->getReturnTypeName();
      if (2 === sscanf($it, '%*[^<]<%[^>]>', $types)) {
        $components= explode(',', $types);
      } else {
        $components= array('var', 'var');
      }
      $e= new Enumerator();
      $e->key= new TypeName(trim($components[0]));
      $e->value= new TypeName(trim($components[1]));
      $e->holder= $this; 
      return $e;
    }

    return null;
  }
  
  /**
   * Create a type name object from a type name string. Corrects old 
   * usages of the type name
   *
   * @param   string t
   * @return  xp.compiler.types.TypeName
   */
  protected function typeNameOf($t) {
    if ('mixed' === $t || '*' === $t || null === $t || 'resource' === $t) {
      return TypeName::$VAR;
    } else if ('self' === $t) {
      return new TypeName($this->class->getName());
    } else if (0 == strncmp($t, 'array', 5)) {
      return new TypeName('var[]');
    }
    return new TypeName($t);
  }

  /**
   * Returns whether a constructor exists
   *
   * @return  bool
   */
  public function hasConstructor() {
    return $this->class->hasConstructor();
  }
  
  /**
   * Returns the constructor
   *
   * @return  xp.compiler.types.Constructor
   */
  public function getConstructor() {
    if (!$this->class->hasConstructor()) return null;
    
    with ($constructor= $this->class->getConstructor()); {
      $c= new Constructor();
      $c->modifiers= $constructor->getModifiers();
      $c->parameters= array();
      foreach ($constructor->getParameters() as $p) {
        $c->parameters[]= $this->typeNameOf($p->getTypeName());
      }
      $c->holder= $this;  
      return $c;
    }
  }

  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasMethod($name) {
    return $this->class->hasMethod($name);
  }
  
  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Method
   */
  public function getMethod($name) {
    if (!$this->class->hasMethod($name)) return null;

    with ($method= $this->class->getMethod($name)); {
      $m= new Method();
      $m->name= $method->getName();
      $m->returns= $this->typeNameOf($method->getReturnTypeName());
      $m->modifiers= $method->getModifiers();
      $m->parameters= array();
      foreach ($method->getParameters() as $p) {
        $m->parameters[]= $this->typeNameOf($p->getTypeName());
      }
      $m->holder= $this;
      return $m;
    }
  }

  /**
   * Returns a typename for a given literal
   *
   * @param  string literal
   * @return string name
   */
  protected function typeName($literal) {
    if ('þ' === $literal[0]) {           // Primitives
      return substr($literal, 1);
    } else if ('¦' === $literal[0]) {    // Arrays
      return $this->nameOf(substr($literal, 1)).'[]';
    } else if ('»' === $literal[0]) {    // Maps
      return '[:'.$this->nameOf(substr($literal, 1)).']';
    } else {                             // Classes, enums, interfaces
      return \xp::nameOf($literal);
    }
  }

  /**
   * Gets a list of extension methods
   *
   * @return  [:xp.compiler.types.Method[]]
   */
  public function getExtensions() {
    $name= $this->literal();

    // Extension methods are registered via __import()
    if (!method_exists($name, '__import')) return array();
    call_user_func(array($name, '__import'), 0);
    if (!isset(\xp::$ext[0])) return array();

    // Found extension methods imported into the 0-scope
    $methods= $this->class->getMethods();
    $r= array();
    foreach (\xp::$ext[0] as $type => $name) {
      $type= $this->typeName($type);
      $r[$type]= array();
      foreach ($methods as $method) {
        if (
          ($method->getModifiers() & MODIFIER_STATIC) &&
          ($method->numParameters() > 0) &&
          ($type === $method->getParameter(0)->getTypeName())
        ) $r[$type][]= $this->getMethod($method->getName());
      }
    }
    unset(\xp::$ext[0]);
    return $r;
  }

  public static $ovl= array(
    '~'   => 'concat',
    '-'   => 'minus',
    '+'   => 'plus',
    '*'   => 'times',
    '/'   => 'div',
    '%'   => 'mod',
  );

  /**
   * Returns whether an operator by a given symbol exists
   *
   * @param   string symbol
   * @return  bool
   */
  public function hasOperator($symbol) {
    return isset(self::$ovl[$symbol]) ? $this->class->hasMethod('operator··'.self::$ovl[$symbol]) : false;
  }
  
  /**
   * Returns an operator by a given name
   *
   * @param   string symbol
   * @return  xp.compiler.types.Operator
   */
  public function getOperator($symbol) {
    if (!$this->hasOperator($symbol)) return null;

    with ($method= $this->class->getMethod('operator··'.self::$ovl[$symbol])); {
      $m= new Method();
      $m->name= $method->getName();
      $m->returns= $this->typeNameOf($method->getReturnTypeName());
      $m->modifiers= $method->getModifiers();
      $m->parameters= array();
      foreach ($method->getParameters() as $p) {
        $m->parameters[]= $this->typeNameOf($p->getTypeName());
      }
      $m->holder= $this;
      return $m;
    }
  }

  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasField($name) {
    return $this->class->hasField($name);
  }
  
  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Field
   */
  public function getField($name) {
    if (!$this->class->hasField($name)) return null;
    
    with ($field= $this->class->getField($name)); {
      $f= new Field();
      $f->name= $field->getName();
      $f->modifiers= $field->getModifiers();
      if ($this->class->isEnum() && ($f->modifiers & (MODIFIER_PUBLIC | MODIFIER_STATIC))) {
        $member= $field->get(null);
        if ($this->class->isInstance($member)) {
          $f->type= $this->typeNameOf($this->class->getName());
        } else {
          $f->type= $this->typeNameOf($field->getTypeName());
        }
      } else {
        $f->type= $this->typeNameOf($field->getTypeName());
      }
      $f->holder= $this;
      return $f;
    }
  }

  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasProperty($name) {
    return false;
  }
  
  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Property
   */
  public function getProperty($name) {
    return null;
  }

  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasConstant($name) {
    return $this->class->hasConstant($name);
  }
  
  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Constant
   */
  public function getConstant($name) {
    if (!$this->class->hasConstant($name)) return null;
      
    with ($constant= $this->class->getConstant($name)); {
      $c= new Constant();
      $c->name= $name;
      $c->type= new TypeName(Type::forName(gettype($constant))->getName());
      $c->value= $constant;
      $c->holder= $this;
      return $c;
    }
  }

  /**
   * Returns whether this class has an indexer
   *
   * @return  bool
   */
  public function hasIndexer() {
    return $this->class->_reflect->implementsInterface('ArrayAccess');
  }

  /**
   * Returns indexer
   *
   * @return  xp.compiler.types.Indexer
   */
  public function getIndexer() {
    if (!$this->class->_reflect->implementsInterface('ArrayAccess')) return null;

    with ($method= $this->class->getMethod('offsetGet')); {
      $i= new Indexer();
      $i->type= $this->typeNameOf($method->getReturnTypeName());
      $i->parameter= $this->typeNameOf($method->getParameter(0)->getTypeName());
      $i->holder= $this;
      return $i;
    }
  }

  /**
   * Returns a lookup map of generic placeholders
   *
   * @return  [:int]
   */
  public function genericPlaceholders() {
    $lookup= array();
    foreach ($this->class->genericComponents() as $i => $name) {
      $lookup[$name]= $i;
    }
    return $lookup;
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */    
  public function toString() {
    return $this->getClassName().'@('.$this->class->toString().')';
  }
}
