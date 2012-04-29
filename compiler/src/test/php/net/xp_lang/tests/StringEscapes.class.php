<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.emit.Strings'
  );

  /**
   * TestCase
   *
   * @see   xp://xp.compiler.emit.Strings
   */
  class StringEscapes extends TestCase {

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

    /**
     * Test "\n"
     *
     */
    #[@test]
    public function newLine() {
      $this->assertEquals("Hello\nWorld", Strings::expandEscapesIn('Hello\nWorld'));
    }
    
    /**
     * Test "\�" is not a legal escape sequence
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegal() {
      Strings::expandEscapesIn('\�');
    }

    /**
     * Test with an empty string
     *
     */
    #[@test]
    public function emptyString() {
      $this->assertEquals('', Strings::expandEscapesIn(''));
    }

    /**
     * Test "\r"
     *
     */
    #[@test]
    public function carriageReturn() {
      $this->assertEquals("Hello\015World", Strings::expandEscapesIn('Hello\rWorld'));
    }

    /**
     * Test "\t"
     *
     */
    #[@test]
    public function tab() {
      $this->assertEquals("Hello\011World", Strings::expandEscapesIn('Hello\tWorld'));
    }

    /**
     * Test "\b"
     *
     */
    #[@test]
    public function backspace() {
      $this->assertEquals("Hello\010World", Strings::expandEscapesIn('Hello\bWorld'));
    }

    /**
     * Test "\f"
     *
     */
    #[@test]
    public function formFeed() {
      $this->assertEquals("Hello\014World", Strings::expandEscapesIn('Hello\fWorld'));
    }

    /**
     * Test "\0"
     *
     */
    #[@test]
    public function nul() {
      $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\0World'));
    }

    /**
     * Test "\00"
     *
     */
    #[@test]
    public function octalNulTwo() {
      $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\00World'));
    }

    /**
     * Test "\000"
     *
     */
    #[@test]
    public function octalNulThree() {
      $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\000World'));
    }

    /**
     * Test "\x0"
     *
     */
    #[@test]
    public function hexNulOne() {
      $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\x0World'));
    }

    /**
     * Test "\x00"
     *
     */
    #[@test]
    public function hexNulTwo() {
      $this->assertEquals("Hello\000World", Strings::expandEscapesIn('Hello\x00World'));
    }

    /**
     * Test "\377" octal escape (0xFF)
     *
     */
    #[@test]
    public function octalFF() {
      $this->assertEquals("Hello\377World", Strings::expandEscapesIn('Hello\377World'));
    }

    /**
     * Test "\xff" octal escape (0xFF)
     *
     */
    #[@test]
    public function hexFFLowercase() {
      $this->assertEquals("Hello\377World", Strings::expandEscapesIn('Hello\xffWorld'));
    }

    /**
     * Test "\xff" octal escape (0xFF)
     *
     */
    #[@test]
    public function hexFFUppercasecase() {
      $this->assertEquals("Hello\377World", Strings::expandEscapesIn('Hello\xFFWorld'));
    }

    /**
     * Test "\400" octal escape is out of range
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function octalNumberOutOfRange() {
      Strings::expandEscapesIn('Hello\400World');
    }

    /**
     * Test "\xFFFF" hex escape is out of range
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function hexNumberOutOfRange() {
      Strings::expandEscapesIn('Hello\xFFFFWorld');
    }

    /**
     * Test "\\"
     *
     */
    #[@test]
    public function backslash() {
      $this->assertEquals('\\', Strings::expandEscapesIn('\\\\'));
    }

    /**
     * Test "\"
     *
     */
    #[@test]
    public function singleBackslash() {
      $this->assertEquals('\\', Strings::expandEscapesIn('\\'));
    }

    /**
     * Test "\\"
     *
     */
    #[@test]
    public function backslashInside() {
      $this->assertEquals('Hello\\World', Strings::expandEscapesIn('Hello\\\\World'));
    }

    /**
     * Test a backslash at the beginning
     *
     */
    #[@test]
    public function leadingBackslash() {
      $this->assertEquals('\\Hello', Strings::expandEscapesIn('\\\\Hello'));
    }

    /**
     * Test a backslash at the end
     *
     */
    #[@test]
    public function trailingBackslash() {
      $this->assertEquals('Hello\\', Strings::expandEscapesIn('Hello\\\\'));
    }

    /**
     * Test a string consisting only of escapes
     *
     */
    #[@test]
    public function escapesOnly() {
      $this->assertEquals("\\\r\n\t", Strings::expandEscapesIn('\\\\\r\n\t'));
    }

    /**
     * Test a string consisting only of one escape
     *
     */
    #[@test]
    public function escapeOnly() {
      foreach (array('\\\\' => '\\', '\r' => "\r", '\n' => "\n", '\t' => "\t") as $in => $out) {
        $this->assertEquals($out, Strings::expandEscapesIn($in));
      }
    }
  }
?>
