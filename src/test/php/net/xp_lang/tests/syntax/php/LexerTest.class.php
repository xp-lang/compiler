<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use lang\Throwable;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.syntax.php.Lexer
 */
abstract class LexerTest extends \unittest\TestCase {

  /**
   * Creates a lexer instance
   *
   * @param   string in
   * @return  xp.compiler.syntax.php.Lexer
   */
  protected abstract function newLexer($in);

  /**
   * Returns an array of tokens for a given input string
   *
   * @param   string in
   * @return  array<int, string>[] tokens
   */
  private function tokensOf($in) {
    $l= $this->newLexer('<?php '.$in.'?>');
    $tokens= [];
    do {
      try {
        if ($r= $l->advance()) {
          $tokens[]= [$l->token, $l->value, $l->position];
        }
      } catch (Throwable $e) {
        $tokens[]= [$e->getClassName(), $e->getMessage()];
        $r= false;
      }
    } while ($r);
    return $tokens;
  }

  /**
   * Provides values for escape_expansion test
   *
   * @return var[][]
   */
  private function escapes() {
    return [
      ['r', "\x0d"],
      ['n', "\x0a"],
      ['t', "\x09"],
      ['b', "\x08"],
      ['f', "\x0c"],
      ['\\', "\x5c"]
    ];
  }

  #[@test]
  public function classDeclaration() {
    $t= $this->tokensOf('class Point { }');
    $this->assertEquals([Parser::T_CLASS, 'class', [1, 6]], $t[0]);
    $this->assertEquals([Parser::T_WORD, 'Point', [1, 12]], $t[1]);
    $this->assertEquals([123, '{', [1, 18]], $t[2]);
    $this->assertEquals([125, '}', [1, 20]], $t[3]);
  }

  #[@test]
  public function commentAtEnd() {
    $t= $this->tokensOf('$a++; // HACK');
    $this->assertEquals([Parser::T_VARIABLE, 'a', [1, 6]], $t[0]);
    $this->assertEquals([Parser::T_INC, '++', [1, 8]], $t[1]);
    $this->assertEquals([59, ';', [1, 10]], $t[2]);
  }

  #[@test]
  public function docComment() {
    $t= $this->tokensOf('
      /**
       * Doc-Comment
       *
       * @see http://example.com
       */  
      public void init() { }
    ');
    $this->assertEquals([Parser::T_PUBLIC, 'public', [7, 7]], $t[0]);
    $this->assertEquals([Parser::T_WORD, 'void', [7, 14]], $t[1]);
    $this->assertEquals([Parser::T_WORD, 'init', [7, 19]], $t[2]);
    $this->assertEquals([40, '(', [7, 23]], $t[3]);
    $this->assertEquals([41, ')', [7, 24]], $t[4]);
    $this->assertEquals([123, '{', [7, 26]], $t[5]);
    $this->assertEquals([125, '}', [7, 28]], $t[6]);
  }

  #[@test]
  public function dqString() {
    $t= $this->tokensOf('$s= "Hello World";');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, 'Hello World', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 23]], $t[3]);
  }

  #[@test]
  public function emptyDqString() {
    $t= $this->tokensOf('$s= "";');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, '', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 12]], $t[3]);
  }

  #[@test]
  public function multiLineDqString() {
    $t= $this->tokensOf('$s= "'."\n\n".'";');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, "\n\n", [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [3, 2]], $t[3]);
  }

  #[@test]
  public function dqStringWithEscapes() {
    $t= $this->tokensOf('$s= "\"Hello\", he said";');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, '"Hello", he said', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 30]], $t[3]);
  }

  #[@test, @values('escapes')]
  public function escape_expansion($escape, $expanded) {
    $this->assertEquals(
      [
        [Parser::T_VARIABLE, 's', [1, 6]],
        [61, '=', [1, 8]],
        [Parser::T_STRING, '{'.$expanded.'}', [1, 10]],
        [59, ';', [1, 16]]
      ],
      $this->tokensOf('$s= "{\\'.$escape.'}";')
    );
  }

  #[@test]
  public function illegalEscapeSequence() {
    $t= $this->tokensOf('$s= "Hell\ü";');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals(['lang.FormatException', 'Illegal escape sequence \\ü in Hell\\ü starting at line 1, offset 10'], $t[2]);
  }

  #[@test]
  public function sqString() {
    $t= $this->tokensOf('$s= \'Hello World\';');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, 'Hello World', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 23]], $t[3]);
  }

  #[@test]
  public function emptySqString() {
    $t= $this->tokensOf('$s= \'\';');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, '', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 12]], $t[3]);
  }

  #[@test]
  public function multiLineSqString() {
    $t= $this->tokensOf('$s= \''."\n\n".'\';');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, "\n\n", [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [3, 2]], $t[3]);
  }

  #[@test]
  public function sqStringWithEscapes() {
    $t= $this->tokensOf('$s= \'\\\'Hello\\\', he said\';');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, '\'Hello\', he said', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 30]], $t[3]);
  }

  #[@test]
  public function stringAsLastToken() {
    $t= $this->tokensOf('"Hello World"');
    $this->assertEquals(1, sizeof($t));
    $this->assertEquals([Parser::T_STRING, 'Hello World', [1, 6]], $t[0]);
  }

  #[@test]
  public function unterminatedString() {
    $t= $this->tokensOf('$s= "The end');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals(['lang.IllegalStateException', 'Unterminated string literal starting at line 1, offset 10'], $t[2]);
  }

  #[@test]
  public function dqBackslash() {
    $t= $this->tokensOf('$s= "\\\\";');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, '\\', [1, 10]], $t[2]);
  }

  #[@test]
  public function sqBackslash() {
    $t= $this->tokensOf('$s= \'\\\\\';');
    $this->assertEquals([Parser::T_VARIABLE, 's', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_STRING, '\\', [1, 10]], $t[2]);
  }

  #[@test]
  public function decimalNumber() {
    $t= $this->tokensOf('$i= 1.0;');
    $this->assertEquals([Parser::T_VARIABLE, 'i', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_DECIMAL, '1.0', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 13]], $t[3]);
  }

  #[@test]
  public function illegalDecimalNumber() {
    $t= $this->tokensOf('$i= 1.a;');
    $this->assertEquals([Parser::T_VARIABLE, 'i', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals(['lang.FormatException', 'Illegal decimal number <1.a> starting at line 1, offset 10'], $t[2]);
  }

  #[@test]
  public function hexNumber() {
    $t= $this->tokensOf('$i= 0xFF;');
    $this->assertEquals([Parser::T_VARIABLE, 'i', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals([Parser::T_HEX, '0xFF', [1, 10]], $t[2]);
    $this->assertEquals([59, ';', [1, 14]], $t[3]);
  }

  #[@test]
  public function illegalHexNumber() {
    $t= $this->tokensOf('$i= 0xZ;');
    $this->assertEquals([Parser::T_VARIABLE, 'i', [1, 6]], $t[0]);
    $this->assertEquals([61, '=', [1, 8]], $t[1]);
    $this->assertEquals(['lang.FormatException', 'Illegal hex number <0xZ> starting at line 1, offset 10'], $t[2]);
  }
}
