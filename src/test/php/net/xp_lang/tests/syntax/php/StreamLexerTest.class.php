<?php namespace net\xp_lang\tests\syntax\php;

/**
 * Tests the lexer tokenizing a stream
 *
 */
class StreamLexerTest extends LexerTest {

  /**
   * Creates a lexer instance
   *
   * @param   string $in
   * @return  xp.compiler.syntax.php.Lexer
   */
  protected function newLexer($in) {
    return new \xp\compiler\syntax\php\Lexer(new \io\streams\MemoryInputStream($in), $this->name);
  }
}
