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
      $sources= array();
      foreach ($this->package->getClassNames() as $name) {
        $sources[]= new SourceClass(
          $name, 
          $this->package->getResourceAsStream(strtr($name, '.', '/').xp::CLASS_FILE_EXT)->getInputStream()
        );
      }
      return $sources;
    }
  }
?>
