<?php namespace net\xp_lang\tests\syntax\php;

use lang\IllegalStateException;

class StringTest extends ParserTestCase {

  #[@test, @expect(IllegalStateException::class)]
  public function unterminated_dq_tring() {
    $this->parse('"Hello World');
  }

  #[@test, @expect(IllegalStateException::class)]
  public function unterminated_sq_string() {
    $this->parse("'Hello World");
  }
}
