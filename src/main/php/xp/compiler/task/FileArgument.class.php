<?php namespace xp\compiler\task;

use io\File;
use xp\compiler\Syntax;
use lang\IllegalArgumentException;

/**
 * Source implementation based on files
 */
class FileArgument extends \lang\Object implements Argument {
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
   * Get sources
   *
   * @return  xp.compiler.io.Source
   */
  public function getSources() {
    return array(new \xp\compiler\io\FileSource($this->file, $this->syntax));
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->file->toString().' @ '.$this->syntax->toString().'>';
  }
}