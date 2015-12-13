<?php namespace net\xp_lang\tests\syntax\xp;

use lang\IllegalStateException;

/**
 * Tests strings
 */
class StringTest extends ParserTestCase {

  #[@test, @expect(IllegalStateException::class)]
  public function unterminated_sq_tring() {
    $this->parse("'Hello World");
  }

  #[@test, @expect(IllegalStateException::class)]
  public function unterminated_dq_string() {
    $this->parse('"Hello World');
  }
}
