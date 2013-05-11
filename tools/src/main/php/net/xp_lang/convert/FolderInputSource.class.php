<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_lang.convert.FileBasedInputSource', 
    'net.xp_lang.convert.SourceClassesInCollection', 
    'io.Folder',
    'io.collections.FileCollection'
  );

  /**
   * Input source classes from a single folder
   *
   */
  class FolderInputSource extends FileBasedInputSource {
    protected $collection= NULL;
    
    /**
     * Constructor
     *
     * @param   io.Folder folder
     */
    public function __construct(Folder $folder) {
      $this->collection= new FileCollection($folder);
    }    

    /**
     * Returns an iterator on sources
     *
     * @return  net.xp_lang.convert.SourceClass[]
     */
    public function getSources() {
      return new SourceClassesInCollection($this->collection);
    }
  }
?>
