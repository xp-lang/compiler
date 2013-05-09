<?php namespace xp\compiler\php;

/**
 * PHP 5.3 Syntax (no alternative syntax)
 */
class Syntax extends \xp\compiler\Syntax {

  /**
   * Creates a parser instance
   *
   * @return  text.parser.generic.AbstractParser
   */
  protected function newParser() {
    return new Parser();
  }

  /**
   * Creates a lexer instance
   *
   * @param   io.streams.InputStream in
   * @param   string source
   * @return  text.parser.generic.AbstractLexer
   */
  protected function newLexer(\io\streams\InputStream $in, $source) {
    return new Lexer($in, $source);
  }
}