<?php namespace xp\compiler\io;

use lang\IClassLoader;
use xp\compiler\Syntax;

/**
 * Source implementation based on class loaders
 *
 */
class ClassLoaderSource extends \lang\Object implements Source {
  protected $loader= null;
  protected $name= '';
  protected $syntax= null;
  
  /**
   * Constructor
   *
   * @param   lang.IClassLoader loader
   * @param   string name
   * @param   xp.compiler.Syntax s Syntax to use
   */
  public function __construct(IClassLoader $loader, $name, Syntax $syntax) {
    $this->loader= $loader;
    $this->name= $name;
    $this->syntax= $syntax;
  }
  
  /**
   * Get input stream
   *
   * @return  io.streams.InputStream
   */
  public function getInputStream() {
    return $this->loader->getResourceAsStream($this->name.'.'.$this->syntax->name())->getInputStream();
  }
  
  /**
   * Get syntax
   *
   * @return  xp.compiler.Syntax
   */
  public function getSyntax() {
    return $this->syntax;
  }

  /**
   * Get URI of this sourcefile - as source in error messages and
   * warnings.
   *
   * @return  string
   */
  public function getURI() {
    return $this->loader->instanceId().$this->name;
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      '%s(%s, resource= %s.%s)',
      $this->getClassName(),
      $this->loader->toString(),
      $this->name,
      $this->syntax->name()
    );
  }

  /**
   * Creates a hashcode of this object
   *
   * @return  string
   */
  public function hashCode() {
    return 'C:'.$this->getURI();
  }
}