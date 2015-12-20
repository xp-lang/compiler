<?php namespace net\xp_lang\tests;

use xp\compiler\Runner;
use xp\compiler\ast\ParseTree;
use xp\compiler\types\Scope;
use util\cmd\Console;
use io\streams\MemoryOutputStream;
use lang\ClassLoader;
use lang\Throwable;

class RunnerTest extends \unittest\TestCase {

  /**
   * Helper which runs the compiler
   *
   * @param  string[] $args
   * @return [:var]
   */
  protected function run($args= []) {
    $saved= ['out' => Console::$out->getStream(), 'err' => Console::$err->getStream()];
    Console::$out->setStream($out= new MemoryOutputStream());
    Console::$err->setStream($err= new MemoryOutputStream());

    try {
      $exit= Runner::main($args);
      return ['exit' => $exit, 'out' => $out->getBytes(), 'err' => $err->getBytes()];
    } finally {
      Console::$out->setStream($saved['out']);
      Console::$err->setStream($saved['err']);
    }
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
    $this->assertMatches('Usage:', $this->run()['err']);
  }

  #[@test]
  public function run_without_parameters_yields_nonzero_exitcode() {
    $this->assertEquals(2, $this->run()['exit']);
  }

  #[@test]
  public function run_with_short_help_shows_usage_on_stderr() {
    $this->assertMatches('Usage:', $this->run(['-?'])['err']);
  }

  #[@test]
  public function run_with_long_help_shows_usage_on_stderr() {
    $this->assertMatches('Usage:', $this->run(['--help'])['err']);
  }

  #[@test, @values([
  #  'util.cmd.Console::writeLine("Hello World")',
  #  'util.cmd.Console::writeLine("Hello World");'
  #])]
  public function evaluate($source) {
    $this->assertEquals(
      ['exit' => 0, 'out' => "Hello World\n", 'err' => ''],
      $this->run(['-e', 'xp', $source])
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
      ['exit' => 0, 'out' => "Hello World\n", 'err' => ''],
      $this->run(['-w', 'xp', $source])
    );
  }

  #[@test]
  public function syntax_error_yields_nonzero_exitcode() {
    $this->assertEquals(1, $this->run(['-e', 'xp', '@syntax error@'])['exit']);
  }

  #[@test]
  public function syntax_error_shows_error_on_stdout() {
    $this->assertMatches(
      'Syntax error at Command line argument',
      $this->run(['-e', 'xp', '@syntax error@'])['out']
    );
  }

  #[@test]
  public function select_v54_emitter() {
    $this->assertEquals(
      "Test\n",
      $this->run(['-E', 'php5.4', '-w', 'xp', '"Test"'])['out']
    );
  }

  #[@test]
  public function select_userdefined_emitter() {
    ClassLoader::defineClass('xp.compiler.emit.test.Emitter', 'xp.compiler.emit.php.V54Emitter', [], [
      'emit' => function(ParseTree $tree, Scope $scope) {
        Console::writeLine('Test emitter emitting...');
        return parent::emit($tree, $scope);
      }
    ]);
    $this->assertEquals(
      "Test emitter emitting...\nTest\n",
      $this->run(['-E', 'test', '-w', 'xp', '"Test"'])['out']
    );
  }

  #[@test]
  public function select_non_existant_emitter() {
    $this->assertMatches(
      'No emitter named "@non_existant@"',
      $this->run(['-E', '@non_existant@', '-w', 'xp', '"Test"'])['err']
    );
  }

  #[@test]
  public function select_non_existant_emitter_version() {
    $this->assertMatches(
      'No emitter named "php5.1"',
      $this->run(['-E', 'php5.1', '-w', 'xp', '"Test"'])['err']
    );
  }

  #[@test]
  public function select_illegal_emitter() {
    $this->assertMatches(
      'Cannot use emitter named "php"',
      $this->run(['-E', 'php', '-w', 'xp', '"Test"'])['err']
    );
  }

  #[@test]
  public function select_non_emitter() {
    ClassLoader::defineClass('xp.compiler.emit.non.V1Emitter', 'lang.Object', []);
    $this->assertMatches(
      'Not an emitter implementation',
      $this->run(['-E', 'non1', '-w', 'xp', '"Test"'])['err']
    );
  }
}