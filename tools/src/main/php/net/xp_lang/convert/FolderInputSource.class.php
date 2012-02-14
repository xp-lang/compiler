<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.convert.FileBasedInputSource', 'io.Folder');

  /**
   * Input source classes from a single folder
   *
   */
  class FolderInputSource extends FileBasedInputSource {
    protected $folder= NULL;
    
    /**
     * Constructor
     *
     * @param   io.Folder folder
     */
    public function __construct(Folder $folder) {
      $this->folder= $folder;
    }    

    /**
     * Returns an iterator on sources
     *
     * @return  net.xp_lang.convert.SourceClass[]
     */
    public function getSources() {
      raise('lang.MethodNotImplementedException', __METHOD__);
    }
  }
?>
