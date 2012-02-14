<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.convert.InputSource', 'lang.reflect.Package');

  /**
   * Input source classes from a package
   *
   */
  class PackageInputSource extends Object implements net·xp_lang·convert·InputSource {
    protected $package= NULL;
    
    /**
     * Constructor
     *
     * @param   lang.reflect.Package package
     */
    public function __construct(Package $package) {
      $this->package= $package;
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
