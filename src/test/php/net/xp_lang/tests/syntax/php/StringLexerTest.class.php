<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Lexer;

class StringLexerTest extends LexerTest {

  /**
   * Creates a lexer instance
   *
   * @param   string $in
   * @return  xp.compiler.syntax.php.Lexer
   */
  protected function newLexer($in) { return new Lexer($in, $this->name); }
}