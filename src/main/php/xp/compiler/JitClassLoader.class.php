<?php namespace xp\compiler;

use xp\compiler\io\ClassLoaderSource;

class JitClassLoader extends \lang\Object implements \lang\IClassLoader {
  protected $source= array();

  static function __static() {
    \lang\XPClass::forName('xp.compiler.Syntax');   // Ensure Syntax class is loaded
  }

  /**
   * Locate a class' sourcecode
   *
   * @param  string $class
   * @return xp.compiler.io.Source or NULL if nothing can be found
   */
  protected function locateSource($class) {
    if (isset($this->source[$class])) return $this->source[$class];

    $name= strtr($class, '.', '/');
    foreach (Syntax::available() as $syntax) {
      foreach (\lang\ClassLoader::getLoaders() as $loader) {
        if ($loader->providesResource($name.'.'.$syntax->name())) {
          return $this->source[$class]= new ClassLoaderSource($loader, $name, $syntax);
        }
      }
    }
    return null;
  }

  /**
   * Checks whether this class loader provides a given class
   *
   * @param  string $class
   * @return bool
   */
  public function providesClass($class) {
    return null !== $this->locateSource($class);
  }

  /**
   * Checks whether this class loader provides a given resource
   *
   * @param  string $filename
   * @return bool
   */
  public function providesResource($filename) {
    return false;
  }

  /**
   * Checks whether this class loader provides a given package
   *
   * @param  string $package
   * @return bool
   */
  public function providesPackage($package) {
    return false;   // TBI
  }

  /**
   * Returns a given package's contents
   *
   * @param  string $package
   * @return string[]
   */
  public function packageContents($package) {
    return array(); // TBI
  }

  public function loadClass($class) {
    return new \lang\XPClass($this->loadClass0($class));
  }
  public function loadClass0($class) {
    if (null === ($source= $this->locateSource($class))) {
      throw new \lang\ClassNotFoundException($class);  
    }
    fputs(STDERR, $source->toString()."\n");
    throw new \lang\ClassNotFoundException($class);
  }
  public function getResource($string) {
    throw new \lang\ElementNotFoundException($string);
  }
  public function getResourceAsStream($string) {
    throw new \lang\ElementNotFoundException($string);
  }
  public function instanceId() {
    return "jit";
  }
}