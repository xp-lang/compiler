<?php namespace xp\compiler\types;

/**
 * Represents a compiled type
 *
 * @test    xp://net.xp_lang.tests.types.CompiledTypeTest
 */
class CompiledType extends Types {
  public $name= null;
  public $parent= null;
  public $kind= null;
  public $indexer= null;
  public $constructor= null;
  public $methods= [];
  public $fields= [];
  public $operators= [];
  public $constants= [];
  public $properties= [];
  public $generics= null;
  public $extensions= [];
  public $modifiers= 0;

  /**
   * Constructor
   *
   * @param   string name
   */
  public function __construct($name= '') {
    $this->name= $name;
  }

  /**
   * Returns modifiers
   *
   * @return int
   */
  public function modifiers() {
    return $this->modifiers;
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
   * Returns name
   *
   * @return  string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Returns literal for use in code
   *
   * @return  string
   */
  public function literal() {
    return $this->literal;
  }
  
  /**
   * Returns literal for use in code
   *
   * @return  string
   */
  public function kind() {
    return $this->kind;
  }

  /**
   * Checks whether a given type instance is a subclass of this class.
   *
   * @param   xp.compiler.types.Types
   * @return  bool
   */
  public function isSubclassOf(Types $t) {
    return $this->parent !== null && ($this->parent->equals($t) || $this->parent->isSubclassOf($t));
  }

  /**
   * Returns whether this type is enumerable (that is: usable in foreach)
   *
   * @see     php://language.oop5.iterations
   * @return  bool
   */
  public function isEnumerable() {
    return false; // TBI
  }

  /**
   * Returns the enumerator for this class or null if none exists.
   *
   * @see     php://language.oop5.iterations
   * @return  xp.compiler.types.Enumerator
   */
  public function getEnumerator() {
    return null;  // TBI
  }
  
  /**
   * Returns whether a constructor exists
   *
   * @return  bool
   */
  public function hasConstructor() {
    return null !== $this->constructor;
  }
  
  /**
   * Returns the constructor
   *
   * @return  xp.compiler.types.Constructor
   */
  public function getConstructor() {
    return $this->constructor;
  }

  /**
   * Adds a method
   *
   * @param   xp.compiler.types.Method method
   * @param   xp.compiler.types.TypeName extension
   * @return  xp.compiler.types.Method the added method
   */
  public function addMethod(Method $method, $extension= null) {
    $method->holder= $this;
    $this->methods[$method->name]= $method;
    if (null !== $extension) {
      $name= $extension->compoundName();
      isset($this->extensions[$name]) || $this->extensions[$name]= [];
      $this->extensions[$name][]= $method;
    }
    return $method;
  }

  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasMethod($name) {
    return isset($this->methods[$name]) || ($this->parent && $this->parent->hasMethod($name));
  }

  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Method
   */
  public function getMethod($name) {
    return isset($this->methods[$name]) 
      ? $this->methods[$name] 
      : ($this->parent ? $this->parent->getMethod($name) : null)
    ;
  }

  /**
   * Gets a list of extension methods
   *
   * @return  [:xp.compiler.types.Method[]]
   */
  public function getExtensions() {
    return $this->extensions;
  }

  /**
   * Adds an operator
   *
   * @param   xp.compiler.types.Operator operator
   * @return  xp.compiler.types.Operator the added operator
   */
  public function addOperator(Operator $operator) {
    $operator->holder= $this;
    $this->operators[$operator->symbol]= $operator;
    return $operator;
  }

  /**
   * Returns whether an operator by a given symbol exists
   *
   * @param   string symbol
   * @return  bool
   */
  public function hasOperator($symbol) {
    return isset($this->operators[$symbol]) || ($this->parent && $this->parent->hasOperator($symbol));
  }
  
  /**
   * Returns an operator by a given name
   *
   * @param   string symbol
   * @return  xp.compiler.types.Operator
   */
  public function getOperator($symbol) {
    return isset($this->operators[$symbol]) 
      ? $this->operators[$symbol] 
      : ($this->parent ? $this->parent->getOperator($symbol) : null)
    ;
  }

  /**
   * Adds a field
   *
   * @param   xp.compiler.types.Field field
   * @return  xp.compiler.types.Field the added field
   */
  public function addField(Field $field) {
    $field->holder= $this;
    $this->fields[$field->name]= $field;
    return $field;
  }

  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasField($name) {
    return isset($this->fields[$name]) || ($this->parent && $this->parent->hasField($name));
  }
  
  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Field
   */
  public function getField($name) {
    return isset($this->fields[$name]) 
      ? $this->fields[$name] 
      : ($this->parent ? $this->parent->getField($name) : null)
    ;
  }

  /**
   * Adds a property
   *
   * @param   xp.compiler.types.Property property
   * @return  xp.compiler.types.Property the added property
   */
  public function addProperty(Property $property) {
    $property->holder= $this;
    $this->properties[$property->name]= $property;
    return $property;
  }

  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasProperty($name) {
    return isset($this->properties[$name]) || ($this->parent && $this->parent->hasProperty($name));
  }
  
  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Property
   */
  public function getProperty($name) {
    return isset($this->properties[$name]) 
      ? $this->properties[$name] 
      : ($this->parent ? $this->parent->getProperty($name) : null)
    ;
  }
  
  /**
   * Adds a constant
   *
   * @param   xp.compiler.types.Constant constant
   * @return  xp.compiler.types.Constant the added constant
   */
  public function addConstant(Constant $constant) {
    $constant->holder= $this;
    $this->constants[$constant->name]= $constant;
    return $constant;
  }

  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasConstant($name) {
    return isset($this->constants[$name]) || $this->parent && $this->parent->hasConstant($name);
  }
  
  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Constant
   */
  public function getConstant($name) {
    return isset($this->constants[$name]) 
      ? $this->constants[$name] 
      : ($this->parent ? $this->parent->getConstant($name) : null)
    ;
  }

  /**
   * Returns whether this class has an indexer
   *
   * @return  bool
   */
  public function hasIndexer() {
    return null !== $this->indexer;
  }

  /**
   * Returns indexer
   *
   * @return  xp.compiler.types.Indexer
   */
  public function getIndexer() {
    return $this->indexer;
  }

  /**
   * Returns a lookup map of generic placeholders
   *
   * @return  [:int]
   */
  public function genericPlaceholders() {
    return $this->generics;
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */    
  public function toString() {
    $s= nameof($this).'<'.$this->name.">@{\n";
    if ($this->constructor) {
      $s.= '  '.$this->constructor->toString()."\n";
    }
    foreach ($this->constants as $constant) {
      $s.= '  '.$constant->toString()."\n";
    }
    foreach ($this->fields as $field) {
      $s.= '  '.$field->toString()."\n";
    }
    foreach ($this->properties as $property) {
      $s.= '  '.$property->toString()."\n";
    }
    if ($this->indexer) {
      $s.= '  '.$this->indexer->toString()."\n";
    }
    foreach ($this->methods as $method) {
      $s.= '  '.$method->toString()."\n";
    }
    foreach ($this->operators as $operator) {
      $s.= '  '.$operator->toString()."\n";
    }
    return $s.'}';
  }
}
