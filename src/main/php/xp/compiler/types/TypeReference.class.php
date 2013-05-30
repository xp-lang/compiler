<?php namespace xp\compiler\types;

/**
 * A reference to a type
 *
 * @test    xp://net.xp_lang.tests.types.TypeReferenceTest
 */
class TypeReference extends Types {
  protected $type= null;
  protected $literal= '';
  protected $kind= 0;
  protected $modifiers= 0;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.types.TypeName
   * @param   int kind
   * @param   int modifiers
   */
  public function __construct(TypeName $type, $kind= parent::CLASS_KIND, $modifiers= 0) {
    $this->type= $type;
    $this->kind= $kind;
    $this->modifiers= $modifiers;
    
    // Calculate type literal
    $p= strrpos($this->type->name, '.');
    $this->literal= false === $p ? $this->type->name : substr($this->type->name, $p+ 1);
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
    return null;
  }
  
  /**
   * Returns name
   *
   * @return  string
   */
  public function name() {
    return $this->type->name;
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
    return false;
  }

  /**
   * Returns whether this type is enumerable (that is: usable in foreach)
   *
   * @return  bool
   */
  public function isEnumerable() {
    return $this->type->isArray() || $this->type->isMap();
  }

  /**
   * Returns the enumerator for this class or null if none exists.
   *
   * @see     php://language.oop5.iterations
   * @return  xp.compiler.types.Enumerator
   */
  public function getEnumerator() {
    if ($this->type->isArray()) {
      $e= new Enumerator();
      $e->key= new TypeName('int');
      $e->value= $this->type->arrayComponentType();
      $e->holder= $this;  
      return $e;
    } else if ($this->type->isMap()) {
      $e= new Enumerator();
      $e->key= new TypeName('string');
      $e->value= $this->type->mapComponentType();
      $e->holder= $this;  
      return $e;
    }

    return null;
  }

  /**
   * Returns whether this class has an indexer
   *
   * @return  bool
   */
  public function hasIndexer() {
    return $this->type->isArray() || $this->type->isMap();
  }

  /**
   * Returns indexer
   *
   * @return  xp.compiler.types.Indexer
   */
  public function getIndexer() {
    if ($this->type->isArray()) {
      $i= new Indexer();
      $i->type= $this->type->arrayComponentType();
      $i->parameter= new Typename('int');
      $i->holder= $this;
      return $i;
    } else if ($this->type->isMap()) {
      $i= new Indexer();
      $i->type= $this->type->mapComponentType();
      $i->parameter= new Typename('string');
      $i->holder= $this;
      return $i;
    }
    return null;
  }

  /**
   * Returns whether a constructor exists
   *
   * @return  bool
   */
  public function hasConstructor() {
    return true;
  }

  /**
   * Returns the constructor
   *
   * @return  xp.compiler.types.Constructor
   */
  public function getConstructor() {
    $c= new Constructor();
    $c->parameters= array();
    $c->holder= $this;
    return $c;
  }

  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasMethod($name) {
    return true;
  }
  
  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Method
   */
  public function getMethod($name) {
    $m= new Method();
    $m->name= $name;
    $m->returns= TypeName::$VAR;
    $m->parameters= array();
    $m->holder= $this;
    return $m;
  }

  /**
   * Gets a list of extension methods
   *
   * @return  [:xp.compiler.types.Method[]]
   */
  public function getExtensions() {
    return array();
  }

  /**
   * Returns whether an operator by a given symbol exists
   *
   * @param   string symbol
   * @return  bool
   */
  public function hasOperator($symbol) {
    return false;
  }
  
  /**
   * Returns an operator by a given name
   *
   * @param   string symbol
   * @return  xp.compiler.types.Operator
   */
  public function getOperator($symbol) {
    return null;
  }

  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  bool
   */
  public function hasField($name) {
    return true;
  }
  
  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Field
   */
  public function getField($name) {
    $m= new Field();
    $m->name= $name;
    $m->type= TypeName::$VAR;
    $m->holder= $this;
    return $m;
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
    return false;
  }
  
  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Constant
   */
  public function getConstant($name) {
    return null;
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
    static $kinds= array(
      self::PRIMITIVE_KIND    => 'PRIMITIVE',
      self::CLASS_KIND        => 'CLASS',
      self::INTERFACE_KIND    => 'INTERFACE',
      self::ENUM_KIND         => 'ENUM',
      self::UNKNOWN_KIND      => 'UNKNOWN',
      self::PARTIAL_KIND      => 'PARTIAL'
    );
    return $this->getClassName().'<'.$kinds[$this->kind].'>@(*->'.$this->type->toString().')';
  }
}
