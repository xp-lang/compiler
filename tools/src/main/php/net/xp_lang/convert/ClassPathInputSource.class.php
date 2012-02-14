<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.convert.FileBasedInputSource');

  /**
   * Input source classes from class path
   *
   */
  class ClassPathInputSource extends FileBasedInputSource {

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
