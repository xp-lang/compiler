<?php namespace net\xp_lang\tests;

use xp\compiler\emit\Strings;
use lang\FormatException;

class StringEscapes extends \unittest\TestCase {

  /**
   * Fail this test case
   *
   * @param   string reason
   * @param   mixed actual
   * @param   mixed expect
   */
  public function fail($reason, $actual, $expect) {
    is_string($actual) && $actual= addcslashes($actual, "\0..\17");
    is_string($expect) && $expect= addcslashes($expect, "\0..\17");
    parent::fail($reason, $actual, $expect);
  }

  #[@test]
  public function newLine() {
    $this->assertEquals("Hello\nWorld", Strings::expandEscapesIn('Hello\nWorld'));
  }
  
  #[@test, @expect(FormatException::class)]
  public function illegal() {
    Strings::expandEscapesIn('\ü');
  }

  #[@test]
  public function emptyString() {
    $this->assertEquals('', Strings::expandEscapesIn(''));
  }

  #[@test]
  public function carriageReturn() {
    $this->assertEquals("Hello\015World", Strings::expandEscapesIn('Hello\rWorld'));
  }

  #[@test]
  public function tab() {
    $this->assertEquals("Hello\011World", Strings::expandEscapesIn('Hello\tWorld'));
  }

  #[@test]
  public function backspace() {
    $this->assertEquals("Hello\010World", Strings::expandEscapesIn('Hello\bWorld'));
  }

  #[@test]
  public function formFeed() {
    $this->assertEquals("Hello\014World", Strings::expandEscapesIn('Hello\fWorld'));
  }

  #[@test]
  public function nul() {
    $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\0World'));
  }

  #[@test]
  public function octalNulTwo() {
    $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\00World'));
  }

  #[@test]
  public function octalNulThree() {
    $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\000World'));
  }

  #[@test]
  public function hexNulOne() {
    $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\x0World'));
  }

  #[@test]
  public function hexNulTwo() {
    $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\x00World'));
  }

  #[@test]
  public function octalFF() {
    $this->assertEquals("Hello\377World", Strings::expandEscapesIn('Hello\377World'));
  }

  #[@test]
  public function hexFFLowercase() {
    $this->assertEquals("Hello\377World", Strings::expandEscapesIn('Hello\xffWorld'));
  }

  #[@test]
  public function hexFFUppercasecase() {
    $this->assertEquals("Hello\377World", Strings::expandEscapesIn('Hello\xFFWorld'));
  }

  #[@test, @expect(FormatException::class)]
  public function octalNumberOutOfRange() {
    Strings::expandEscapesIn('Hello\400World');
  }

  #[@test, @expect(FormatException::class)]
  public function hexNumberOutOfRange() {
    Strings::expandEscapesIn('Hello\xFFFFWorld');
  }

  #[@test]
  public function backslash() {
    $this->assertEquals('\\', Strings::expandEscapesIn('\\\\'));
  }

  #[@test]
  public function singleBackslash() {
    $this->assertEquals('\\', Strings::expandEscapesIn('\\'));
  }

  #[@test]
  public function backslashInside() {
    $this->assertEquals('Hello\\World', Strings::expandEscapesIn('Hello\\\\World'));
  }

  #[@test]
  public function leadingBackslash() {
    $this->assertEquals('\\Hello', Strings::expandEscapesIn('\\\\Hello'));
  }

  #[@test]
  public function trailingBackslash() {
    $this->assertEquals('Hello\\', Strings::expandEscapesIn('Hello\\\\'));
  }

  #[@test]
  public function escapesOnly() {
    $this->assertEquals("\\\r\n\t", Strings::expandEscapesIn('\\\\\r\n\t'));
  }

  #[@test, @values([
  #  ['\\\\', '\\'],
  #  ['\r', "\r"],
  #  ['\n', "\n"],
  #  ['\t', "\t"]
  #])]
  public function escapeOnly($in, $out) {
    $this->assertEquals($out, Strings::expandEscapesIn($in));
  }
}