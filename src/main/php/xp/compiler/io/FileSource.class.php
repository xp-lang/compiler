<?php namespace xp\compiler\io;

use io\File;
use xp\compiler\Syntax;
use lang\IllegalArgumentException;

/**
 * Source implementation based on files
 */
class FileSource extends \lang\Object implements Source {
  protected $file= null;
  protected $syntax= null;
  
  /**
   * Constructor
   *
   * @param   io.File file
   * @param   xp.compiler.Syntax s Syntax to use, determined via source file's syntax otherwise
   * @throws  lang.IllegalArgumentException in case the syntax cannot be determined
   */
  public function __construct(File $file, Syntax $s= null) {
    $this->file= $file;
    try {
      $this->syntax= $s ?: Syntax::forName($this->file->getExtension());
    } catch (IllegalArgumentException $e) {
      throw new IllegalArgumentException('Cannot determine syntax for "'.$this->file->getFileName().'"', $e);
    }
  }
  
  /**
   * Get input stream
   *
   * @return  io.streams.InputStream
   */
  public function getInputStream() {
    return new \io\streams\FileInputStream($this->file);
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
    return $this->file->getURI();
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return nameof($this).'<'.str_replace(
      realpath(getcwd()), 
      '.', 
      $this->file->getURI()
    ).'>';
  }

  /**
   * Creates a hashcode of this object
   *
   * @return  string
   */
  public function hashCode() {
    return 'S:'.$this->file->getURI();
  }
}