<?php namespace xp\compiler\emit\source;

/**
 * Compilation result from source emitter
 */
class Result extends \lang\Object implements \xp\compiler\emit\EmitterResult {
  protected $source= null;
  protected $type= null;
  
  /**
   * Constructor.
   *
   * @param   xp.compiler.types.Types type
   * @param   xp.compiler.emit.source.Buffer source
   */
  public function __construct(\xp\compiler\Types $type, $source) {
    $this->type= $type;
    $this->source= $source;
  }
  
  /**
   * Write this result to an output stream
   *
   * @param   io.streams.OutputStream out
   */
  public function writeTo(\io\streams\OutputStream $out) {
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
    return \xp::CLASS_FILE_EXT;
  }

  /**
   * Execute with a given environment settings
   *
   * @param   array<string, var> env
   * @return  var
   */    
  public function executeWith(array $env= array()) {
    with ($cl= \lang\DynamicClassLoader::instanceFor(__FUNCTION__), $name= $this->type->name()); {
      $cl->setClassBytes($name, $this->source);
      $cl->loadClass0($name);
    }
  }
}
