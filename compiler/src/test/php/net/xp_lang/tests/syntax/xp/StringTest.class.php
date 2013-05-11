<?php namespace net\xp_lang\tests\syntax\xp;

/**
 * Tests strings
 */
class StringTest extends ParserTestCase {

  #[@test, @expect('lang.IllegalStateException')]
  public function unterminated_sq_tring() {
    $this->parse("'Hello World");
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function unterminated_dq_string() {
    $this->parse('"Hello World');
  }
}
