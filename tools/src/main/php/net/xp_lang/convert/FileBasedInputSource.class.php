<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.convert.InputSource', 'io.File');

  /**
   * Input source based on files
   *
   */
  abstract class FileBasedInputSource extends Object implements net·xp_lang·convert·InputSource {

    /**
     * Determine class name from a file
     *
     * @param   io.File f
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    protected function classNameOf(File $file) {
      $uri= $file->getURI();
      $path= dirname($uri);
      $paths= array_flip(array_map('realpath', xp::$registry['classpath']));
      $class= NULL;
      while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
        if (isset($paths[$path])) {
          return strtr(substr($uri, strlen($path)+ 1, -10), DIRECTORY_SEPARATOR, '.');
          break;
        }

        $path= substr($path, 0, $pos); 
      }
      throw new IllegalArgumentException('Cannot determine class name from '.$file->toString());
    }
  }
?>
