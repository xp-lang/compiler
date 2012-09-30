<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.convert';

  uses('net.xp_lang.convert.SourceClass');

  /**
   * An input source
   *
   */
  interface net·xp_lang·convert·InputSource {

    /**
     * Returns an iterator on sources
     *
     * @return  net.xp_lang.convert.SourceClass[]
     */
    public function getSources();
  }
?>
