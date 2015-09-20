<?php namespace net\xp_lang\tests\syntax\php;

class StringTest extends ParserTestCase {

  #[@test, @expect('lang.IllegalStateException')]
  public function unterminated_dq_tring() {
    $this->parse('"Hello World');
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function unterminated_sq_string() {
    $this->parse("'Hello World");
  }
}
