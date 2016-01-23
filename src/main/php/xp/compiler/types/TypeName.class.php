<?php namespace xp\compiler\types;

/**
 * Represents a type name
 *
 * Type literals and their representation
 * --------------------------------------
 * ```
 * int                   : TypeName('int')
 * var                   : TypeName('var')
 * string                : TypeName('string')
 * bool[]                : TypeName('bool[]')
 * [:var]                : TypeName('[:var]')
 * List<T>               : TypeName('List', [TypeName('T')])
 * Map<K, V>             : TypeName('Map', [TypeName('K'), TypeName('V')])
 * function(int): string : TypeName('->string', [TypeName('int')])
 * ```
 *
 * @test     xp://net.xp_lang.tests.types.TypeNameTest
 * @purpose  Value object
 */
class TypeName extends \lang\Object {
  public
    $name       = '',
    $components = [];
  
  public static $primitives = ['int', 'double', 'bool', 'string'];
  public static $VAR;
  public static $VOID;
  
  static function __static() {
    self::$VAR= new self('var');
    self::$VOID= new self('void');
  }
  
  /**
   * Creates a new typename instance
   *
   * @param   string name
   * @param   xp.compiler.types.TypeName[] components
   */
  public function __construct($name, $components= []) {
    $this->name= $name;
    $this->components= $components;
  }

  /**
   * Return whether this type is an array type
   *
   * @return  bool
   */
  public function isClass() {
    return !$this->isArray() && !$this->isMap() && !$this->isVariable() && !$this->isVoid() && !$this->isPrimitive() && !$this->isFunction();
  }
  
  /**
   * Return whether this type is an array type
   *
   * @return  bool
   */
  public function isArray() {
    return '[]' === substr($this->name, -2);
  }

  /**
   * Return array component type or null if this is not an array
   *
   * @return  xp.compiler.types.TypeName
   */
  public function arrayComponentType() {
    return $this->isArray() ? new self(substr($this->name, 0, -2)) : null;
  }

  /**
   * Return whether this type is a primitive type
   *
   * @return  bool
   */
  public function isPrimitive() {
    return in_array($this->name, self::$primitives);
  }

  /**
   * Return whether this type is a variable type
   *
   * @return  bool
   */
  public function isVariable() {
    return 'var' === $this->name;
  }

  /**
   * Return whether this type is a void type
   *
   * @return  bool
   */
  public function isVoid() {
    return 'void' === $this->name;
  }

  /**
   * Return whether this type is a map
   *
   * @return  bool
   */
  public function isMap() {
    return '[:' === substr($this->name, 0, 2);
  }

  /**
   * Return map component type or null if this is not a map
   *
   * @return  xp.compiler.types.TypeName
   */
  public function mapComponentType() {
    return $this->isMap() ? new self(substr($this->name, 2, -1)) : null;
  }

  /**
   * Return whether this type is a function type
   *
   * @return  bool
   */
  public function isFunction() {
    return 0 === strncmp($this->name, '->', 2);
  }

  /**
   * Return function return type or null if this is not a function
   *
   * @return  xp.compiler.types.TypeName
   */
  public function functionReturnType() {
    return $this->isFunction() ? new self(substr($this->name, 2)) : null;
  }

  /**
   * Return whether this type is a generic
   *
   * @return  bool
   */
  public function isGeneric() {
    return 0 !== strncmp($this->name, '->', 2) && !empty($this->components);
  }

  /**
   * Return whether this type is a placeholder. Given a type name "T" or
   * "T[]", this method will return true if "T" is contained in this 
   * types' components, e.g. as in "List<T>".
   *
   * @param   xp.compiler.types.TypeName ref
   * @return  bool
   */
  public function isPlaceholder(self $ref) {
    if ($ref->isArray()) {
      $cmp= $ref->arrayComponentType();
    } else {
      $cmp= $ref;
    }
    foreach ($this->components as $component) {
      if ($component->name === $cmp->name) return true;
    }
    return false;
  }

  /**
   * Checks whether another object is equal to this type name
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    if (!$cmp instanceof self || $this->name !== $cmp->name) return false;

    foreach ((array)$this->components as $i => $c) {
      if (!$c->equals($cmp->components[$i])) return false;
    }
    return true;
  }
  
  /**
   * Helper for compoundName() and toString()
   *
   * @return  string
   */
  protected function compoundNameOf(self $type) {
    if (!$type->components) return $type->name;

    $s= $type->name.'<';
    foreach ($type->components as $c) {
      $s.= $this->compoundNameOf($c).', ';
    }
    return substr($s, 0, -2).'>';
  }
  
  /**
   * Returns a compound type name
   *
   * @return  string
   */
  public function compoundName() {
    return $this->compoundNameOf($this);
  }
  
  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return nameof($this).'('.$this->compoundNameOf($this).')';
  }
}
