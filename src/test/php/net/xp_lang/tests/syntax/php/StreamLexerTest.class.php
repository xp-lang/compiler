<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Lexer;
use io\streams\MemoryInputStream;

class StreamLexerTest extends LexerTest {

  /**
   * Creates a lexer instance
   *
   * @param   string $in
   * @return  xp.compiler.syntax.php.Lexer
   */
  protected function newLexer($in) { return new Lexer(new MemoryInputStream($in), $this->name); }
}
