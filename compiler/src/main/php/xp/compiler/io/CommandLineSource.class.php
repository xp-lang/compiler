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
  protected $template= '';

  public static $NAME= '_Generated';
  public static $TEMPLATE= array(
    'xp'  => '/** Generated */ public class %s { /** Entry */ public static void main(string[] $args) {%s}}',
    'php' => '<?php /** Generated */ class %s extends \lang\Object { /** Entry */ public static function main(array $args) {%s}}'
  );

  /**
   * Constructor
   *
   * @param   string fragment
   * @param   xp.compiler.Syntax s Syntax to use
   * @param   int offset
   * @throws  lang.IllegalArgumentException
   */
  public function __construct($fragment, \xp\compiler\Syntax $syntax, $offset) {
    $this->fragment= $fragment;
    $this->syntax= $syntax;
    $this->offset= $offset;

    // Verify template
    $name= $this->syntax->name();
    if (!isset(self::$TEMPLATE[$name])) {
      throw new \lang\IllegalArgumentException('No command line code template for syntax "'.$name.'"');
    }
    $this->template= self::$TEMPLATE[$name];
  }
  
  /**
   * Get input stream
   *
   * @return  io.streams.InputStream
   */
  public function getInputStream() {
    return new \io\streams\MemoryInputStream(sprintf(
      $this->template,
      self::$NAME,
      $this->fragment
    ));
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
