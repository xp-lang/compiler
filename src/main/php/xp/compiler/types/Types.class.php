<?php namespace xp\compiler\types;

/**
 * Abstract base class
 *
 */
abstract class Types extends \lang\Object {
  const 
    PRIMITIVE_KIND    = 0,
    CLASS_KIND        = 1,
    INTERFACE_KIND    = 2,
    ENUM_KIND         = 3;
  
  const
    UNKNOWN_KIND      = -1,
    PARTIAL_KIND      = -2;

  /**
   * Returns name
   *
   * @return  string
   */
  public abstract function name();

  /**
   * Returns parent type
   *
   * @return  xp.compiler.types.Types
   */
  public abstract function parent();

  /**
   * Returns literal for use in code
   *
   * @return  string
   */
  public abstract function literal();

  /**
   * Returns type kind (one of the *_KIND constants).
   *
   * @return  string
   */
  public abstract function kind();

  /**
   * Checks whether a given type instance is a subclass of this class.
   *
   * @param   self $t
   * @return  bool
   */
  public abstract function isSubclassOf(Types $t);

  /**
   * Returns whether this type is enumerable (that is: usable in foreach)
   *
   * @return  bool
   */
  public abstract function isEnumerable();

  /**
   * Returns the enumerator for this class or null if none exists.
   *
   * @see     php://language.oop5.iterations
   * @return  xp.compiler.types.Enumerator
   */
  public abstract function getEnumerator();

  /**
   * Returns whether a constructor exists
   *
   * @return  bool
   */
  public abstract function hasConstructor();

  /**
   * Returns the constructor
   *
   * @return  xp.compiler.types.Constructor
   */
  public abstract function getConstructor();

  /**
   * Returns whether a method with a given name exists
   *
   * @param   string name
   * @return  bool
   */
  public abstract function hasMethod($name);
  
  /**
   * Returns a method by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Method
   */
  public abstract function getMethod($name);

  /**
   * Gets a list of extension methods
   *
   * @return  [:xp.compiler.types.Method[]]
   */
  public abstract function getExtensions();

  /**
   * Returns whether an operator by a given symbol exists
   *
   * @param   string symbol
   * @return  bool
   */
  public abstract function hasOperator($symbol);
  
  /**
   * Returns an operator by a given name
   *
   * @param   string symbol
   * @return  xp.compiler.types.Operator
   */
  public abstract function getOperator($symbol);

  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  bool
   */
  public abstract function hasField($name);
  
  /**
   * Returns a field by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Field
   */
  public abstract function getField($name);

  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  bool
   */
  public abstract function hasProperty($name);
  
  /**
   * Returns a property by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Property
   */
  public abstract function getProperty($name);

  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  bool
   */
  public abstract function hasConstant($name);
  
  /**
   * Returns a constant by a given name
   *
   * @param   string name
   * @return  xp.compiler.types.Constant
   */
  public abstract function getConstant($name);

  /**
   * Returns whether this class has an indexer
   *
   * @return  bool
   */
  public abstract function hasIndexer();

  /**
   * Returns indexer
   *
   * @return  xp.compiler.types.Indexer
   */
  public abstract function getIndexer();
  
  /**
   * Returns a lookup map of generic placeholders
   *
   * @return  [:int]
   */
  public abstract function genericPlaceholders();

  /**
   * Returns modifiers
   *
   * @return int
   */
  public abstract function modifiers();

  /**
   * Returns package name
   *
   * @return  string
   */
  public function package() {
    $name= $this->name();
    return substr($name, 0, strrpos($name, '.'));
  }

  /**
   * Test this type for equality with another object
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name() === $cmp->name();
  }
}
