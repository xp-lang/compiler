<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.collections.iterate.FilteredIOCollectionIterator', 
    'io.collections.iterate.ExtensionEqualsFilter',
    'io.collections.iterate.CollectionFilter',
    'io.collections.FileCollection',
    'io.Folder',
    'io.streams.TextReader',
    'util.cmd.Console'
  );

  /**
   * Creates PHP function list
   *
   */
  class FunctionList extends Object {
  
    /**
     * Determine extension name which is inside *.m4
     *
     * <pre>
     *   PHP_NEW_EXTENSION(sybase_ct, php_sybase_ct.c, $ext_shared)
     * </pre>
     *
     * @param   io.collections.FileCollection folder
     * @return  string name
     */
    protected static function extensionName(FileCollection $folder) {
      $name= NULL;
      foreach (new FilteredIOCollectionIterator($folder, new ExtensionEqualsFilter('.m4')) as $m4) {
        $config= new TextReader($m4->getInputStream());
        $name= NULL;
        while (NULL !== ($line= $config->readLine())) {
          if (sscanf(ltrim($line), 'PHP_NEW_EXTENSION(%[^,],', $name) > 0) break;
        }
        $config->close();
        if (NULL !== $name) return $name;
      }
      return NULL;
    }
    
    /**
     * Entry point
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $ext= new Folder($args[0], 'ext');
      $zend= new Folder($args[0], 'Zend');
      if (!$ext->exists() || !$zend->exists()) {
        Console::$err->writeLine('*** Cannot find "ext" and "Zend" folders in ', $args[0]);
        return 1;
      }

      // Scan ext/*/*.h for PHP_FUNCTION
      $headers= new ExtensionEqualsFilter('.h');
      $folders= new FilteredIOCollectionIterator(new FileCollection($ext), new CollectionFilter());
      foreach ($folders as $folder) {
        if (NULL === ($extension= self::extensionName($folder))) {
          Console::$err->writeLine('*** Cannot determine extension name from ', $folder);
          continue;
        }
        Console::$err->writeLine('# ', $extension);
        $files= new FilteredIOCollectionIterator($folder, $headers, FALSE);
        foreach ($files as $file) {
          $reader= new TextReader($file->getInputStream());
          while (NULL !== ($line= $reader->readLine())) {
            sscanf($line, 'PHP_FUNCTION(%[^%)]);', $func) > 0 && Console::writeLine($func, '=', $extension, '.', $func);
          }
          $reader->close();
        }
      }
      
      // Scan Zend/zend_builtin_functions.c
      Console::$err->writeLine('# core');
      $reader= new TextReader(create(new File($zend, 'zend_builtin_functions.c'))->getInputStream());
      while (NULL !== ($line= $reader->readLine())) {
        sscanf($line, 'ZEND_FUNCTION(%[^%)]);', $func) > 0 && Console::writeLine($func, '=', 'core.', $func);
      }
      $reader->close();
      
      return 0;
    }
  }
?>
