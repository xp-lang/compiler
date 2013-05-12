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
   * @param   string syntax
   * @param   string fragment
   * @param   int offset
   * @param   bool return whether to add return statement if not present in fragment
   * @throws  lang.IllegalArgumentException
   */
  public function __construct($syntax, $fragment, $offset, $return= false) {
    $this->syntax= \xp\compiler\Syntax::forName($syntax);
    $this->offset= $offset;

    // Add "return" statement if not present. TODO: If other languages are added
    // in which the string "return" is not the return statement, then this needs
    // to be rewritten
    $this->fragment= rtrim($fragment, ';').';';
    if ($return && !(strstr($fragment, 'return ') || strstr($fragment, 'return;'))) {
      $this->fragment= 'return '.$this->fragment;
    }

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
