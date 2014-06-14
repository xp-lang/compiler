<?php namespace net\xp_lang\tests\syntax\xp;

/**
 * TestCase for parse errors
 *
 */
class ParseErrorTest extends ParserTestCase {

  /**
   * Test standalone double colon
   *
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Unexpected T_DOUBLE_COLON/')]
  public function standaloneDoubleColon() {
    $this->parse('::');
  }

  /**
   * Test unclosed string
   *
   */
  #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/Unterminated string literal/')]
  public function unclosedString() {
    $this->parse('$str= "Hello');
  }

  /**
   * Test PHP object operator ("->") chains
   *
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Unexpected T_ARROW/'), @values([
  #  '$hello->world->result;',
  #  '$hello->world()->result;',
  #  '$hello->world()->emit();'
  #])]
  public function phpObjectOperator($code) {
    $this->parse($code);
  }

  /**
   * Test PHP foreach syntax
   *
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Unexpected T_AS/')]
  public function phpForeachList() {
    $this->parse('foreach ($list as $element) { }');
  }

  /**
   * Test PHP foreach syntax
   *
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Unexpected T_AS/')]
  public function phpForeachMap() {
    $this->parse('foreach ($map as $key => $value) { }');
  }

  /**
   * Test standalone block
   *
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= "/Unexpected '{'/")]
  public function standloneBlock() {
    $this->parse('$i= 0; { $i++; }');
  }
}
