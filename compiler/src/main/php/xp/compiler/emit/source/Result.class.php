<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.emit.source';
  
  uses('xp.compiler.emit.EmitterResult', 'io.streams.OutputStream', 'io.streams.Streams');

  /**
   * Compilation result from source emitter
   *
   */
  class xp·compiler·emit·source·Result extends Object implements EmitterResult {
    protected $source= NULL;
    protected $type= NULL;
    
    /**
     * Constructor.
     *
     * @param   xp.compiler.types.Types type
     * @param   xp.compiler.emit.source.Buffer source
     */
    public function __construct(Types $type, $source) {
      $this->type= $type;
      $this->source= $source;
    }
    
    /**
     * Write this result to an output stream
     *
     * @param   io.streams.OutputStream out
     */
    public function writeTo(OutputStream $out) {
      $out->write('<?php ');
      $out->write($this->source);
      $out->write("\n?>\n");
    }
    
    /**
     * Return type
     *
     * @return  xp.compiler.types.Types type
     */
    public function type() {
      return $this->type;
    }

    /**
     * Return file extension including the leading dot
     *
     * @return  string
     */
    public function extension() {
      return xp::CLASS_FILE_EXT;
    }

    /**
     * Execute with a given environment settings
     *
     * @param   array<string, var> env
     * @return  var
     */    
    public function executeWith(array $env= array()) {
      with ($cl= DynamicClassLoader::instanceFor(__FUNCTION__), $name= $this->type->name()); {
        $cl->setClassBytes($name, $this->source);
        $cl->loadClass0($name);
      }
    }
  }
?>
