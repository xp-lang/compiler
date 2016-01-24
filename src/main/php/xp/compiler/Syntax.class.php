<?php namespace xp\compiler;

use io\streams\InputStream;

/**
 * Syntax base class
 */
abstract class Syntax extends \lang\Object {
  private static $syntaxes= [];
  protected $parser= null;
  protected $name;

  /**
   * Constructor
   *
   * @param  string name
   */
  public function __construct($name) {
    $this->name= $name;
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
   * Register syntaxes
   *
   * @param  lang.reflect.Package[] $packages
   * @return void
   */
  public static function registerAll($packages) {
    foreach ($packages as $package) {
      $name= $package->getSimpleName();
      self::$syntaxes[$name]= $package->loadClass('Syntax')->newInstance($name);
    }
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
  public function parse(InputStream $in, $source= null, $messages= null) {
    if (null === $this->parser) {
      $this->parser= $this->newParser();
    }

    $result= $this->parser->parse($this->newLexer($in, $source ? $source : \xp::stringOf($in)));
    if ($messages) foreach ($this->parser->getWarnings() as $warning) {
      $messages->warn(
        sprintf('P%03d', $warning->code),
        $warning->message.($warning->expected ? ', expected '.implode(', ', $warning->expected) : '')
      );
    }
    return $result;
  }
  
  /**
   * Creates a string representation
   *
   * @return  string
   */
  public function toString() {
    return nameof($this).'('.$this->hashCode().')';
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
