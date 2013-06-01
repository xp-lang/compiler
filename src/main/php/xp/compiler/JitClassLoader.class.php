<?php namespace xp\compiler;

use xp\compiler\types\TypeName;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\ClassLoaderSource;
use xp\compiler\task\CompilationTask;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\Syntax;

class JitClassLoader extends \lang\Object implements \lang\IClassLoader {
  protected $source= array();

  static function __static() {
    uses('xp.compiler.Syntax', 'io.File');   // HACK: Ensure Syntax and File classes are loaded
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

  /**
   * Loads a class
   *
   * @param  string $class
   * @return lang.XPClass
   * @throws lang.ClassLoadingException
   */
  public function loadClass($class) {
    return new \lang\XPClass($this->loadClass0($class));
  }

  /**
   * Compiles a class if necessary
   *
   * @param  string $class
   * @return string
   * @throws lang.ClassLoadingException
   */
  public function loadClass0($class) {
    if (isset(\xp::$cl[$class])) return \xp::reflect($class);

    // Locate sourcecode
    if (null === ($source= $this->locateSource($class))) {
      throw new \lang\ClassNotFoundException($class);  
    }

    // Parse, then emit source
    // fputs(STDERR, "COMPILE ".$source->toString()."...\n");
    $emitter= new \xp\compiler\emit\source\Emitter();
    $scope= new TaskScope(new CompilationTask(
      $source,
      new NullDiagnosticListener(),
      new FileManager(),
      $emitter
    ));
    try {
      $r= $emitter->emit($source->getSyntax()->parse($source->getInputStream()), $scope);
    } catch (\lang\Throwable $e) {
      throw new \lang\ClassFormatException('Cannot compile '.$source->getURI(), $e);
    }

    // Clean up
    unset($this->source[$class]);

    // Define type
    $r->executeWith(array());
    \xp::$cl[$class]= $this->getClassName().'://'.$this->instanceId();
    return $r->type()->literal();
  }

  /**
   * Gets a resource
   *
   * @param  string $string name
   * @return string
   * @throws lang.ElementNotFoundException
   */
  public function getResource($string) {
    throw new \lang\ElementNotFoundException($string);
  }

  /**
   * Gets a resource as a stream
   *
   * @param  string $string name
   * @return io.Stream
   * @throws lang.ElementNotFoundException
   */
  public function getResourceAsStream($string) {
    throw new \lang\ElementNotFoundException($string);
  }

  /**
   * Get unique identifier for this class loader
   *
   * @return string
   */
  public function instanceId() {
    return 'jit';
  }

  /**
   * Fetch instance of classloader by path
   *
   * @param   string path the identifier
   * @return  lang.IClassLoader
   */
  public static function instanceFor($path) {
    static $pool= array();

    if (!isset($pool[$path])) {
      $pool[$path]= new self($path);
    }
    return $pool[$path];
  }

  /**
   * Gets a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName();
  }
}