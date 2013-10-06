<?php namespace net\xp_lang\tests;

use xp\compiler\Runner;
use util\cmd\Console;
use io\streams\MemoryOutputStream;

class RunnerTest extends \unittest\TestCase {

  /**
   * Helper which runs the compiler
   *
   * @param  string[] $args
   * @return [:var]
   */
  protected function run($args= array()) {
    $saved= array('out' => Console::$out->getStream(), 'err' => Console::$err->getStream());
    Console::$out->setStream($out= new MemoryOutputStream());
    Console::$err->setStream($err= new MemoryOutputStream());

    try {
      $exit= Runner::main($args);
      $r= array('exit' => $exit, 'out' => $out->getBytes(), 'err' => $err->getBytes());
    } catch (\lang\Throwable $t) {
      // Fall through
    } ensure($t); {
      Console::$out->setStream($saved['out']);
      Console::$err->setStream($saved['err']);
      if ($t) throw ($t);
    }
    
    return $r;
  }

  /**
   * Assertion helper
   *
   * @param  string $pattern A regular expression
   * @param  string $string The input string
   * @throws unittest.AssertionFailedErrir
   */
  protected function assertMatches($pattern, $string) {
    $this->assertEquals(1, preg_match('/'.$pattern.'/', $string), 'matches /'.$pattern.'/');
  }
  
  #[@test]
  public function run_without_parameters_shows_usage_on_stderr() {
    $this->assertMatches('Usage:', this($this->run(), 'err'));
  }

  #[@test]
  public function run_without_parameters_yields_nonzero_exitcode() {
    $this->assertEquals(2, this($this->run(), 'exit'));
  }

  #[@test]
  public function run_with_short_help_shows_usage_on_stderr() {
    $this->assertMatches('Usage:', this($this->run(array('-?')), 'err'));
  }

  #[@test]
  public function run_with_long_help_shows_usage_on_stderr() {
    $this->assertMatches('Usage:', this($this->run(array('--help')), 'err'));
  }

  #[@test, @values([
  #  'util.cmd.Console::writeLine("Hello World")',
  #  'util.cmd.Console::writeLine("Hello World");'
  #])]
  public function evaluate($source) {
    $this->assertEquals(
      array('exit' => 0, 'out' => "Hello World\n", 'err' => ''),
      $this->run(array('-e', 'xp', $source))
    );
  }

  #[@test, @values([
  #  '"Hello World"',
  #  '"Hello World";',
  #  'return "Hello World"',
  #  'return "Hello World";'
  #])]
  public function write($source) {
    $this->assertEquals(
      array('exit' => 0, 'out' => "Hello World\n", 'err' => ''),
      $this->run(array('-w', 'xp', $source))
    );
  }

  #[@test]
  public function syntax_error_yields_nonzero_exitcode() {
    $this->assertEquals(1, this($this->run(array('-e', 'xp', '@syntax error@')), 'exit'));
  }

  #[@test]
  public function syntax_error_shows_error_on_stdout() {
    $this->assertMatches(
      'Syntax error at Command line argument',
      this($this->run(array('-e', 'xp', '@syntax error@')), 'out')
    );
  }

  #[@test]
  public function select_v52_emitter() {
    $this->assertEquals(
      "Test\n",
      this($this->run(array('-E', 'php5.2', '-w', 'xp', '"Test"')), 'out')
    );
  }

  #[@test]
  public function select_v53_emitter() {
    $this->assertEquals(
      "Test\n",
      this($this->run(array('-E', 'php5.3', '-w', 'xp', '"Test"')), 'out')
    );
  }

  #[@test]
  public function select_userdefined_emitter() {
    \lang\ClassLoader::defineClass('xp.compiler.emit.test.Emitter', 'xp.compiler.emit.php.V53Emitter', array(), '{
      public function emit(\xp\compiler\ast\ParseTree $tree, xp\compiler\types\Scope $scope) {
        Console::writeLine("Test emitter emitting...");
        return parent::emit($tree, $scope);
      }
    }');
    $this->assertEquals(
      "Test emitter emitting...\nTest\n",
      this($this->run(array('-E', 'test', '-w', 'xp', '"Test"')), 'out')
    );
  }

  #[@test]
  public function select_non_existant_emitter() {
    $this->assertMatches(
      'No emitter named "@non_existant@"',
      this($this->run(array('-E', '@non_existant@', '-w', 'xp', '"Test"')), 'err')
    );
  }

  #[@test]
  public function select_non_existant_emitter_version() {
    $this->assertMatches(
      'No emitter named "php5.1"',
      this($this->run(array('-E', 'php5.1', '-w', 'xp', '"Test"')), 'err')
    );
  }

  #[@test]
  public function select_illegal_emitter() {
    $this->assertMatches(
      'Cannot use emitter named "php"',
      this($this->run(array('-E', 'php', '-w', 'xp', '"Test"')), 'err')
    );
  }

  #[@test]
  public function select_non_emitter() {
    \lang\ClassLoader::defineClass('xp.compiler.emit.non.V1Emitter', 'lang.Object', array(), '{
      // Empty
    }');
    $this->assertMatches(
      'Not an emitter implementation',
      this($this->run(array('-E', 'non1', '-w', 'xp', '"Test"')), 'err')
    );
  }
}