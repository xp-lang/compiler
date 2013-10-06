<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests ARM blocks
 *
 */
class ArmTest extends ExecutionTest {

  #[@test]
  public function closes_on_success() {
    $this->assertTrue($this->run('
      $s= new lang.Closeable() {
        public bool $closed= false;
        public void close() { $this.closed= true; }
      };

      try ($s) {
        // Intentionally empty
      }
      return $s.closed;
    '));
  }

  #[@test]
  public function closes_on_exception() {
    $this->assertTrue($this->run('
      $s= new lang.Closeable() {
        public bool $closed= false;
        public void close() { $this.closed= true; }
      };

      try {
        try ($s) {
          throw new lang.XPException("Blam!");
        }
      } catch (lang.XPException $expected) {
        return $s.closed;
      }
      return false;
    '));
  }

  #[@test]
  public function handles_exceptions_from_close_and_closes_all_closeables() {
    $this->assertTrue($this->run('
      $s= new lang.Closeable() {
        public bool $closed= false;
        public void close() { $this.closed= true; }
      };
      $t= new lang.Closeable() {
        public void close() { throw new lang.XPException("Blam!"); }
      };

      try ($t, $s) {
        // Intentionally empty
      }
      return $s.closed;
    '));
  }
}
