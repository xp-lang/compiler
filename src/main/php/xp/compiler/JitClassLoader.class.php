<?php namespace xp\compiler;

use xp\compiler\types\TypeName;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\ClassLoaderSource;
use xp\compiler\task\CompilationTask;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\Syntax;
use io\Folder;
use io\File;

/**
 * JIT ("Just in time") compiling class loader. Enables the efficient
 * "edit / save / run" paradigm at development time.
 */
class JitClassLoader extends \lang\Object implements \lang\IClassLoader {
  protected $source= array();
  protected $files= null;

  static function __static() {
    uses('xp.compiler.Syntax', 'io.File');   // HACK: Ensure Syntax and File classes are loaded
  }

  /**
   * Creates a JIT Class loader instances for a given path
   *
   * @param string $path
   */
  public function __construct($path) {
    $this->files= new FileManager();

    // Maven conventions
    $path= rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    foreach (array('src/main/xp', 'src/test/xp') as $dir) {
      if (is_dir($d= realpath($path.$dir))) {
        $this->files->addSourcePath($d.DIRECTORY_SEPARATOR);
      }
    }

    // Current directory
    $this->files->addSourcePath($path);

    // Output, "target" is Maven conformant, "dist" used in several projects
    $output= $path;
    foreach (array('target', 'dist') as $dir) {
      if (is_dir($d= realpath($path.$dir))) {
        $output= $d;
      }
    }
    $this->files->setOutput(new Folder($output));
  }

  /**
   * Locate a class' sourcecode
   *
   * @param  string $class
   * @return xp.compiler.io.Source or NULL if nothing can be found
   */
  protected function locateSource($class) {
    if (!isset($this->source[$class])) {
      if (null === ($source= $this->files->findClass($class))) return null;
      $this->source[$class]= $source;
    }
    return $this->source[$class];
  }

  /**
   * Checks whether this class loader provides a given class
   *
   * @param  string $class
   * @return bool
   */
  public function providesClass($class) {
    if (null === ($source= $this->locateSource($class))) return false;

    $origin= new File($source->getURI());
    $target= new File($this->files->getOutput(), strtr($class, '.', DIRECTORY_SEPARATOR).\xp::CLASS_FILE_EXT);

    // If the target does not exist or the source is newer, it needs to be compiled.
    // Otherwise, no action needs to be taken.
    return $target->exists() ? $origin->lastModified() > $target->lastModified() : true;
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
    return null !== $this->files->findPackage($package);
  }

  /**
   * Returns a given package's contents
   *
   * @param  string $package
   * @return string[]
   */
  public function packageContents($package) {

    // Calculate syntax regex for matching on files
    static $syntaxes= '';
    if (!$syntaxes) {
      foreach (Syntax::available() as $syntax) {
        $syntaxes.= '|'.$syntax->name();
      }
      $syntaxes= '/\.('.substr($syntaxes, 1).')$/';
    }

    // List directory contents, replacing compileable source files with 
    // class file names. These of course don't exist yet, but will be 
    // compiled on demand
    $return= array();
    $dir= strtr($package, '.', DIRECTORY_SEPARATOR);
    foreach ($this->files->getSourcePaths() as $path) {
      if (!is_dir($d= $path.$dir.DIRECTORY_SEPARATOR)) continue;

      $handle= opendir($d);
      while ($e= readdir($handle)) {
        if ('.' === $e || '..' === $e) {
          continue;
        } else if (is_dir($d.$e)) {
          $return[]= $e.'/';
        } else if (strstr($e, \xp::CLASS_FILE_EXT)) {
          $return[]= $e;
        } else {
          $return[]= preg_replace($syntaxes, \xp::CLASS_FILE_EXT, $e);
        }
      }
      closedir($handle);
    }
    return $return;
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
    // DEBUG fputs(STDERR, "COMPILE ".$source->toString()."\n");
    $emitter= new \xp\compiler\emit\source\Emitter();
    $scope= new TaskScope(new CompilationTask(
      $source,
      new NullDiagnosticListener(),
      $this->files,
      $emitter
    ));
    try {
      $r= $emitter->emit($source->getSyntax()->parse($source->getInputStream()), $scope);
      $this->files->write($r, $this->files->getTarget($r, $source));
    } catch (\lang\Throwable $e) {
      // DEBUG $e->printStackTrace(STDERR);
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
    return 'jit:'.implode('|', $this->files->getSourcePaths());
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
    return $this->getClassName().'<'.implode(', ', $this->files->getSourcePaths()).' -> '.$this->files->getOutput()->toString().'>';
  }
}