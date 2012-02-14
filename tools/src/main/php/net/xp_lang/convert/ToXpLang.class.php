<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
 
  $package= 'net.xp_lang.convert';
 
  uses(
    'util.cmd.Command',
    'net.xp_lang.convert.SourceConverter',
    'net.xp_lang.convert.ClassPathInputSource',
    'net.xp_lang.convert.FolderInputSource',
    'net.xp_lang.convert.FileInputSource',
    'net.xp_lang.convert.PackageInputSource',
    'io.FileUtil',
    'io.streams.TextReader',
    'io.streams.InputStream'
  );

  /**
   * Convert a given class file to XP Language
   *
   */
  class net·xp_lang·convert·ToXpLang extends Command {
    protected $input= NULL;
    protected $target= NULL;
    protected $converter= NULL;
    
    /**
     * Creates converter
     *
     */
    public function __construct() {
      $this->converter= new SourceConverter();
    }
  
    /**
     * Sets input
     *
     * @param   string input
     */
    #[@arg(position= 0)]
    public function setInput($input= '*') {
      if ('*' === $input) {
        $this->input= new ClassPathInputSource();
      } else if (is_dir($input)) {
        $this->input= new FolderInputSource(new Folder($input));
      } else if (is_file($input)) {
        $this->input= new FileInputSource(new File($input));
      } else {
        $this->input= new PackageInputSource(Package::forName($input));
      }
    }

    /**
     * Sets output
     *
     * @param   string output
     */
    #[@arg(short= 'O')]
    public function setOutput($output= '.') {
      $this->target= new Folder($output);
    }

    /**
     * Sets additional name map to load
     *
     * @param   string file
     */
    #[@arg]
    public function setNameMap($file= NULL) {
      $file && $this->loadNameMap($this->converter->nameMap, create(new File($file))->getInputStream());
    }
    
    /**
     * Loads name map
     *
     * @param   util.collections.HashTable<string, string> map
     * @param   io.streams.InputStream
     * @return  int
     */
    protected function loadNameMap($map, InputStream $i) {
      $r= new TextReader($i);
      $mappings= 0;
      while (NULL !== ($line= $r->readLine())) {
        sscanf($line, '%[^=]=%s', $name, $qualified);
        $map[$name]= $qualified;
        $mappings++;
      }
      $r->close();
      return $mappings;
    }
    
    /**
     * Retrieve an output stream for a give class name
     *
     * @param   string name
     * @return  io.streams.OutputStream
     */
    protected function outputStreamFor($name) {
      $target= new File($this->target, str_replace('.', DIRECTORY_SEPARATOR, $name).'.xp');
      $place= new Folder($target->getPath());
      $place->exists() || $place->create();

      return $target->getOutputStream();
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      $this->loadNameMap($this->converter->nameMap, $this->getClass()->getPackage()->getResourceAsStream('name.map')->getInputStream());
      $this->loadNameMap($this->converter->funcMap, $this->getClass()->getPackage()->getResourceAsStream('func.map')->getInputStream());

      $this->err->write('-> ', $this->target, '[');
      foreach ($this->input->getSources() as $class) {
        $out= NULL;
        try {
          $name= $class->getName();
          $result= $this->converter->convert($name, token_get_all(Streams::readAll($class->getInputStream())));
          $out= $this->outputStreamFor($name);
          $out->write($result);
          $this->err->write('.');
        } catch (Throwable $e) {
          $this->err->writeLine($e);
        } finally(); {
          $out && $out->close();
        }
      }
      $this->err->write(']');
    }
  }
?>
