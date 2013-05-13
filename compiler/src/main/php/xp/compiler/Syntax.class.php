<?php namespace xp\compiler;

use io\streams\InputStream;
use lang\reflect\Package;

/**
 * Syntax base class
 */
abstract class Syntax extends \lang\Object {
  private static $syntaxes= array();
  protected $parser= null;
  protected $name;
  
  static function __static() {
    foreach (Package::forName('xp.compiler.syntax')->getPackages() as $syntax) {
      $name= $syntax->getSimpleName();
      self::$syntaxes[$name]= $syntax->loadClass('Syntax')->newInstance($name);
    }
  }
  
  /**
   * Constructor
   *
   * @param  string name
   */
  public function __construct($name) {
    $this->name= $name;
    $this->parser= $this->newParser();
  }

  /**
   * Get name
   *
   * @return string name
   */
  public function name() {
    return $this->name;
  }
  
  /**
   * Retrieve a syntax for a given name
   *
   * @param   string syntax
   * @return  xp.compiler.syntax.Compiler
   * @throws  lang.IllegalArgumentException If syntax is not supported
   */
  public static function forName($syntax) {
    if (!isset(self::$syntaxes[$syntax])) {
      throw new \lang\IllegalArgumentException('Syntax "'.$syntax.'" not supported');
    }
    return self::$syntaxes[$syntax];
  }

  /**
   * Retrieve a list of available syntaxes
   *
   * @return  array<string, xp.compiler.Syntax>
   */
  public static function available() {
    return self::$syntaxes;
  }
  
  /**
   * Parse
   *
   * @param   io.streams.InputStream in
   * @param   string source default null
   * @return  xp.compiler.ast.ParseTree tree
   */
  public function parse(InputStream $in, $source= null) {
    return $this->parser->parse($this->newLexer($in, $source ? $source : $in->toString()));
  }
  
  /**
   * Creates a string representation
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'('.$this->hashCode().')';
  }
  
  /**
   * Creates a parser instance
   *
   * @return  text.parser.generic.AbstractParser
   */
  protected abstract function newParser();

  /**
   * Creates a lexer instance
   *
   * @param   io.streams.InputStream in
   * @param   string source
   * @return  text.parser.generic.AbstractLexer
   */
  protected abstract function newLexer(InputStream $in, $source);
}
