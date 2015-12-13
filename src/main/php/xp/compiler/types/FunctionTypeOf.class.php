<?php namespace xp\compiler\types;

class FunctionTypeOf extends Types {
  protected $return= null;
  protected $parameters= null;
  
  /**
   * Constructor
   *
   * @param   xp.compiler.types.Types $return
   * @param   xp.compiler.types.Types[] $parameters
   */
  public function __construct(Types $return, $parameters) {
    $this->return= $return;
    $this->parameters= $parameters;
  }

  /**
   * Returns modifiers
   *
   * @return int
   */
  public function modifiers() {
    return MODIFIER_PUBLIC;
  }

  /**
   * Returns name
   *
   * @return  string
   */
  public function name() {
    return sprintf(
      'function(%s): %s',
      null === $this->parameters ? '?' : implode(', ', array_map(function($type) { return $type->name(); }, $this->parameters)),
      $this->return->name()
    );
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
   * Returns literal for use in code
   *
   * @return  string
   */
  public function literal() {
    return 'callable';
  }

  /**
   * Returns type kind (one of the *_KIND constants).
   *
   * @return  string
   */
  public function kind() {
    return self::FUNCTION_KIND;
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
    return false;
  }

  /**
   * Returns the enumerator for this class or null if none exists.
   *
   * @see     php://language.oop5.iterations
   * @return  xp.compiler.types.Enumerator
   */
  public function getEnumerator() {
    return null;
  }

  /**
   * Returns whether a constructor exists
   *
   * @return  bool
   */
  public function hasConstructor() {
    return false;
  }

  /**
   * Returns the constructor
   *
   * @return  xp.compiler.types.Constructor
   */
  public function getConstructor() {
    return null;
  }

  /**
   * Returns whether a method with a given name exists
   *
   * @param   string name
   * @return  bool
   */
  public function hasMethod($name) {
    return false;
  }

  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Method
   */
  public function getMethod($name) {
    return null;
  }

  /**
   * Gets a list of extension methods
   *
   * @return  [:xp.compiler.types.Method[]]
   */
  public function getExtensions() {
    return [];
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
    return false;
  }
  
  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Field
   */
  public function getField($name) {
    return null;
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
   * Returns whether this class has an indexer
   *
   * @return  bool
   */
  public function hasIndexer() {
    return false;
  }

  /**
   * Returns indexer
   *
   * @return  xp.compiler.types.Indexer
   */
  public function getIndexer() {
    return null;
  }

  /**
   * Returns a lookup map of generic placeholders
   *
   * @return  [:int]
   */
  public function genericPlaceholders() {
    return [];
  }
  
  /**
   * Creates a string representation of this object
   *
   * @return  string
   */    
  public function toString() {
    return sprintf(
      '%s@(%s)',
      $this->getClassName(),
      $this->name()
    );
  }
}
