<?php namespace xp\compiler\task;

use xp\compiler\Syntax;
use io\Folder;
use io\File;
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\ExtensionEqualsFilter;
use io\collections\iterate\AnyOfFilter;

/**
 * Source implementation based on folder
 */
class FolderArgument extends \lang\Object implements Argument {
  protected $folder= null;
  protected $recursive= false;

  /**
   * Constructor
   *
   * @param   io.File file
   * @param   xp.compiler.Syntax s Syntax to use, determined via source file's syntax otherwise
   * @throws  lang.IllegalArgumentException in case the syntax cannot be determined
   */
  public function __construct(Folder $folder, $recursive= true) {
    $this->folder= $folder;
    $this->recursive= $recursive;
  }
  
  /**
   * Get sources
   *
   * @return  xp.compiler.io.Source
   */
  public function getSources() {
    static $filter= null;

    if (null === $filter) {
      $filter= new AnyOfFilter();
      foreach (Syntax::available() as $ext => $syntax) {
        $filter->add(new ExtensionEqualsFilter($ext));
      }
    }
    
    $files= array();
    $it= new FilteredIOCollectionIterator(new FileCollection($this->folder), $filter, $this->recursive);
    foreach ($it as $element) {
      $files[]= new \xp\compiler\io\FileSource(new File($element->getURI()));
    }
    return $files;
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->folder->toString().($this->recursive ? ' +R' : '').'>';
  }
}