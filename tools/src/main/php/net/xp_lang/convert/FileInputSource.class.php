<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.convert.FileBasedInputSource', 'io.File');

  /**
   * Input source classes from a single file
   *
   */
  class FileInputSource extends FileBasedInputSource {
    protected $file= NULL;
    
    /**
     * Constructor
     *
     * @param   io.File file
     */
    public function __construct(File $file) {
      $this->file= $file;
    }    

    /**
     * Returns an iterator on sources
     *
     * @return  net.xp_lang.convert.SourceClass[]
     */
    public function getSources() {
      return array(new SourceClass(
        self::classNameOf($this->file->getURI()), 
        $this->file->getInputStream()
      ));
    }
  }
?>
