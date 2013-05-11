<?php namespace net\xp_lang\tests\execution\source;

use io\streams\MemoryOutputStream;
use util\cmd\Console;

/**
 * Tests static imports
 */
class StaticImportTest extends ExecutionTest {
  protected $stream, $out= null;

  /**
   * Set up testcase and redirect console output to a memory stream
   */
  public function setUp() {
    parent::setUp();
    $this->stream= new MemoryOutputStream();
    $this->out= Console::$out->getStream();
    Console::$out->setStream($this->stream);
  }
  
  /**
   * Tears down test case
   */
  public function tearDown() {
    Console::$out->setStream($this->out);
    delete($this->stream);
  }

  /**
   * Test util.cmd.Console.*
   *
   */
  #[@test]
  public function importAll() {
    $this->run(
      'writeLine("Hello");', 
      array('import static util.cmd.Console::*;')
    );
    $this->assertEquals("Hello\n", $this->stream->getBytes());
  }

  /**
   * Test util.cmd.Console.writeLine
   *
   */
  #[@test]
  public function importSpecific() {
    $this->run(
      'writeLine("Hello");', 
      array('import static util.cmd.Console::writeLine;')
    );
    $this->assertEquals("Hello\n", $this->stream->getBytes());
  }

  /**
   * Test peer.http.HttpConstants.*;
   *
   */
  #[@test]
  public function importConst() {
    $this->run(
      'util.cmd.Console::writeLine(STATUS_OK);', 
      array('import static peer.http.HttpConstants::*;')
    );
    $this->assertEquals("200\n", $this->stream->getBytes());
  }

  /**
   * Test self.*;
   *
   */
  #[@test]
  public function importSelf() {
    $class= $this->define('class', 'ImportSelfTest', null, '{
      public static string join(string $a, string $b) {
        return $a ~ " " ~ $b;
      }
      
      public string run() {
        return join("Hello", "World");
      }
    }', array('import static self::*;'));
    $this->assertEquals('Hello World', $class->newInstance()->run());
  }
}
