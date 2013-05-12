<?php namespace xp\compiler\io;

/**
 * Source implementation
 *
 * @test    xp://net.xp_lang.tests.StringSourceTest
 */
class CommandLineSource extends \lang\Object implements Source {
  protected $source= null;
  protected $name= null;
  protected $syntax= null;

  public static $NAME= '_Generated';
  public static $TEMPLATE= '/** Generated */ class %s { /** Entry */ public static void main(string[] $args) {%s}}';

  /**
   * Constructor
   *
   * @param   string fragment
   * @param   xp.compiler.Syntax s Syntax to use
   * @param   int offset
   */
  public function __construct($fragment, \xp\compiler\Syntax $syntax, $offset) {
    $this->fragment= $fragment;
    $this->syntax= $syntax;
    $this->offset= $offset;
  }
  
  /**
   * Get input stream
   *
   * @return  io.streams.InputStream
   */
  public function getInputStream() {
    return new \io\streams\MemoryInputStream(sprintf(self::$TEMPLATE, self::$NAME, $this->fragment));
  }
  
  /**
   * Get syntax
   *
   * @return  xp.compiler.Syntax
   */
   
  public function getSyntax() {
    return $this->syntax;
  }

  /**
   * Get URI of this source - as source in error messages and
   * warnings.
   *
   * @return  string
   */
  public function getURI() {
    return 'Command line arg #'.$this->offset;
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<arg #'.$this->offset.'>';
  }

  /**
   * Creates a hashcode of this object
   *
   * @return  string
   */
  public function hashCode() {
    return 'C:'.$this->offset;
  }
}
