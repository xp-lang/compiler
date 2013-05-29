<?php namespace net\xp_lang\tests\syntax\php;

/**
 * Tests the lexer tokenizing string input
 *
 */
class StringLexerTest extends LexerTest {

  /**
   * Creates a lexer instance
   *
   * @param   string $in
   * @return  xp.compiler.syntax.php.Lexer
   */
  protected function newLexer($in) {
    return new \xp\compiler\syntax\php\Lexer($in, $this->name);
  }
}