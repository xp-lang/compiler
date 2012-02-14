<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.collections.IOCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter'
  );

  /**
   * A source class iterator based on an IOCollection
   *
   */
  class SourceClassesInCollection extends Object implements Iterator {
    protected $files= NULL;
    protected $key= 0;

    /**
     * Constructor
     *
     * @param   io.collections.IOCollection collection
     */
    public function __construct($collection) {
      $this->files= new FilteredIOCollectionIterator(
        $collection, 
        new ExtensionEqualsFilter(xp::CLASS_FILE_EXT),
        TRUE
      );
    }

    /**
     * Returns current element
     *
     * @return  net.xp_lang.convert.SourceClass
     */
    public function current() {
      $element= $this->files->next();
      return new SourceClass(
        FileBasedInputSource::classNameOf($element->getURI()),
        $element->getInputStream()
      );
    }

    /**
     * Returns key
     *
     * @return  int
     */
    public function key() { 
      return $this->key; 
    }

    /**
     * Forwards iteration
     *
     */
    public function next() {
      $this->key++;
    }

    /**
     * Rewinds iteration
     *
     */
    public function rewind() {
      /* NOOP */ 
    }

    /**
     * Checks whether iteration can continue
     *
     * @return  bool
     */
    public function valid() { 
      return $this->files->hasNext();
    }
  }
?>
