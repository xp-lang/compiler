<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.collections.iterate.FilteredIOCollectionIterator', 
    'io.collections.iterate.ExtensionEqualsFilter',
    'io.collections.FileCollection',
    'io.streams.TextReader',
    'util.cmd.Console'
  );

  /**
   * Creates name map
   *
   */
  class NameMap extends Object {
    const SCAN_FOR_PACKAGE_LINES = 7;
    
    /**
     * Entry point
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $classes= new ExtensionEqualsFilter(xp::CLASS_FILE_EXT);
      $end= -strlen(xp::CLASS_FILE_EXT);
      foreach (ClassLoader::getLoaders() as $loader) {
        $base= new FileCollection($loader->path);
        Console::$err->writeLine($base);
        $offset= strlen($base->getURI());
        foreach (new FilteredIOCollectionIterator($base, $classes, TRUE) as $element) {
          $name= strtr(substr($element->getURI(), $offset, $end), DIRECTORY_SEPARATOR, '.');
        
          // Check if this is a fully qualified class
          $r= new TextReader($element->getInputStream());
          $package= NULL;
          for ($i= 0; $i < self::SCAN_FOR_PACKAGE_LINES; $i++) {
            $line= $r->readLine();
            if (1 === sscanf($line, "  \$package= '%[^']';", $package)) break;
          }
          $r->close();
        
          // Map name
          Console::writeLine($package ? strtr($package, '.', '·').'·' : '', substr($name, strrpos($name, '.')+ 1), '=', $name);
        }
      }
    }
  }
?>
