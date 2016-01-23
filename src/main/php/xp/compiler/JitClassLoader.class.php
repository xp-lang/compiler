<?php namespace xp\compiler;

use xp\compiler\types\TypeName;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\ClassLoaderSource;
use xp\compiler\task\CompilationTask;
use xp\compiler\emit\php\Emitter;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\Syntax;
use text\parser\generic\ParseException;
use lang\ClassNotFoundException;
use lang\ClassFormatException;
use lang\FormatException;
use lang\ElementNotFoundException;
use lang\XPClass;

/**
 * JIT ("Just in time") compiling class loader. Enables the efficient
 * "edit / save / run" paradigm at development time.
 */
class JitClassLoader extends \lang\Object implements \lang\IClassLoader {
  protected $source= [];
  protected $files= null;
  protected $emitter= null;
  protected $debug= false;

  /**
   * Creates a JIT Class loader instances for a given path
   *
   * @param string $path
   * @param bool $debug
   */
  public function __construct($path, $debug= false) {
    $this->files= new FileManager();
    $this->debug= $debug;

    // Maven conventions
    $path= rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    foreach (['src/main/xp', 'src/test/xp'] as $dir) {
      if (is_dir($d= realpath($path.$dir))) {
        $this->files->addSourcePath($d.DIRECTORY_SEPARATOR);
      }
    }

    // Current directory
    $this->files->addSourcePath($path);
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
   * Checks whether this class loader provides a given uri
   *
   * @param  string $uri
   * @return bool
   */
  public function providesUri($uri) {
    return false;
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
    return null !== $this->files->findPackage($package);
  }

  /**
   * Returns a given package's contents
   *
   * @param  string $package
   * @return string[]
   */
  public function packageContents($package) {
    static $syntaxes= '';

    // Calculate syntax regex for matching on files
    if (!$syntaxes) {
      foreach (Syntax::available() as $syntax) {
        $syntaxes.= '|'.$syntax->name();
      }
      $syntaxes= '/\.('.substr($syntaxes, 1).')$/';
    }

    // List directory contents, replacing compileable source files with 
    // class file names. These of course don't exist yet, but will be 
    // compiled on demand
    $return= [];
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
    if (isset(\xp::$cl[$class])) return literal($class);

    // Locate sourcecode
    if (null === ($source= $this->locateSource($class))) {
      throw new ClassNotFoundException($class);  
    }

    if (null === $this->emitter) {
      $this->emitter= Emitter::newInstance();
    }

    // Parse, then emit source
    $this->debug && fputs(STDERR, "COMPILE ".$source->toString()."\n");
    $scope= new TaskScope(new CompilationTask(
      $source,
      new NullDiagnosticListener(),
      $this->files,
      $this->emitter
    ));
    $this->emitter->clearMessages();
    try {
      $r= $this->emitter->emit($source->getSyntax()->parse($source->getInputStream()), $scope);
    } catch (ParseException $e) {
      $this->debug && $e->printStackTrace();
      throw new ClassFormatException('Cannot compile '.$source->getURI().': '.$e->formattedErrors(''), $e);
    } catch (FormatException $e) {
      $this->debug && $e->printStackTrace();
      throw new JitCompilationError($class, [$this], $this->emitter->messages(), $e);
    }

    // Clean up
    unset($this->source[$class]);

    // Define type
    $this->debug && fputs(STDERR, $r->type()->toString()."\n");
    $r->executeWith([]);
    \xp::$cl[$class]= nameof($this).'://'.$this->instanceId();
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
    throw new ElementNotFoundException($string);
  }

  /**
   * Gets a resource as a stream
   *
   * @param  string $string name
   * @return io.Stream
   * @throws lang.ElementNotFoundException
   */
  public function getResourceAsStream($string) {
    throw new ElementNotFoundException($string);
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
  public static function instanceFor($path, $debug= false) {
    static $pool= [];

    if (!isset($pool[$path.$debug])) {
      $pool[$path.$debug]= new self($path, $debug);
    }
    return $pool[$path.$debug];
  }

  /**
   * Gets a string representation
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'<'.implode(', ', $this->files->getSourcePaths()).'>';
  }
}
