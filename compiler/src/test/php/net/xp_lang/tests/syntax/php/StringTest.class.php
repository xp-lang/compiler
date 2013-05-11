<?php namespace net\xp_lang\tests\syntax\php;


/**
 * TestCase
 */
class StringTest extends ParserTestCase {

  /**
   * Test unterminated string
   */
  #[@test, @expect('lang.IllegalStateException')]
  public function unterminated_dq_tring() {
    $this->parse('"Hello World');
  }

  /**
   * Test unterminated string
   */
  #[@test, @expect('lang.IllegalStateException')]
  public function unterminated_sq_string() {
    $this->parse("'Hello World");
  }
}
