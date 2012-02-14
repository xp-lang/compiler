<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_lang.convert.FileBasedInputSource', 
    'net.xp_lang.convert.SourceClassesInCollection',
    'io.collections.FileCollection',
    'io.collections.ArchiveCollection',
    'io.collections.CollectionComposite'
  );

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
      $collections= array();
      foreach (ClassLoader::getLoaders() as $loader) {
        if ($loader instanceof FileSystemClassLoader) {
          $collections[]= new FileCollection($loader->path);
        } else if ($loader instanceof ArchiveClassLoader) {
          $collections[]= new ArchiveCollection($loader->path);
        }
      }
      return new SourceClassesInCollection(new CollectionComposite($collections));
    }
  }
?>
