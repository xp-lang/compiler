<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
 
  $package= 'cmd.convert';
 
  uses(
    'util.cmd.Command',
    'cmd.convert.SourceConverter',
    'io.File',
    'io.FileUtil',
    'io.streams.TextReader',
    'io.streams.InputStream'
  );

  /**
   * Convert a given class file to XP Language
   *
   */
  class cmd·convert·ToXpLang extends Command {
    protected $file= '';
    protected $converter= NULL;
    
    /**
     * Creates converter
     *
     */
    public function __construct() {
      $this->converter= new SourceConverter();
    }
  
    /**
     * Sets file to convert
     *
     * @param   string file
     */
    #[@arg(position= 0)]
    public function setInput($file) {
      $this->file= new File($file);
    }

    /**
     * Sets additional name map to load
     *
     * @param   string file
     */
    #[@arg]
    public function setNameMap($file= NULL) {
      $file && $this->loadNameMap(create(new File($file))->getInputStream());
    }
    
    /**
     * Determine class
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
    
    /**
     * Loads name map
     *
     * @param   io.streams.InputStream
     * @return  int
     */
    protected function loadNameMap(InputStream $i) {
      $r= new TextReader($i);
      $mappings= 0;
      while (NULL !== ($line= $r->readLine())) {
        sscanf($line, '%[^=]=%s', $name, $qualified);
        $this->converter->nameMap[$name]= $qualified;
        $mappings++;
      }
      $r->close();
      return $mappings;
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      $this->loadNameMap($this->getClass()->getPackage()->getResourceAsStream('name.map')->getInputStream());
      try {
        $this->out->writeLine($this->converter->convert(
          $this->classNameOf($this->file), 
          token_get_all(FileUtil::getContents($this->file))
        ));
      } catch (Throwable $e) {
        $this->err->writeLine($e);
      }
    }
  }
?>
