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
}