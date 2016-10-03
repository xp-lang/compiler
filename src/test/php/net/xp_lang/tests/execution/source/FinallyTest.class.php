<?php namespace net\xp_lang\tests\execution\source;

class FinallyTest extends ExecutionTest {
  
  #[@test]
  public function tryFinallyNoException() {
    $executed= $this->run('
      $r= new util.collections.Vector();
      try {
        $r[]= "Try";
      } finally {
        $r[]= "Finally";
      }
      return $r;
    ');
    $this->assertEquals(['Try', 'Finally'], $executed->elements());
  }

  #[@test]
  public function tryFinallyWithException() {
    $executed= $this->run('
      try {
        $r= new util.collections.Vector();
        try {
          $r[]= "Try";
          throw new FormatException("Error");
        } finally {
          $r[]= "Finally";
        }
      } catch (FormatException $e) {
        $r[]= "Catch";
      }
      return $r;
    ');
    $this->assertEquals(['Try', 'Finally', 'Catch'], $executed->elements());
  }

  #[@test]
  public function tryFinallyWithReturn() {
    $executed= $this->run('
      $r= new util.collections.Vector();
      try {
        $r[]= "Try";
        return $r;
      } finally {
        $r[]= "Finally";
      }
    ');
    $this->assertEquals(['Try', 'Finally'], $executed->elements());
  }

  #[@test]
  public function tryCatchFinallyWithException() {
    $executed= $this->run('
      $r= new util.collections.Vector();
      try {
        $r[]= "Try";
        throw new FormatException("Error");
      } catch (FormatException $e) {
        $r[]= "Catch"; 
      } finally {
        $r[]= "Finally";
      }
      return $r;
    ');
    $this->assertEquals(['Try', 'Catch', 'Finally'], $executed->elements());
  }

  #[@test]
  public function tryCatchFinallyWithReturnInsideCatch() {
    $executed= $this->run('
      $r= new util.collections.Vector();
      try {
        $r[]= "Try";
        throw new FormatException("Error");
      } catch (FormatException $e) {
        $r[]= "Catch"; 
        return $r;
      } finally {
        $r[]= "Finally";
      }
    ');
    $this->assertEquals(['Try', 'Catch', 'Finally'], $executed->elements());
  }

  #[@test]
  public function tryCatchFinallyNoException() {
    $executed= $this->run('
      $r= new util.collections.Vector();
      try {
        $r[]= "Try";
      } catch (FormatException $e) {
        $r[]= "Catch"; 
      } finally {
        $r[]= "Finally";
      }
      return $r;
    ');
    $this->assertEquals(['Try', 'Finally'], $executed->elements());
  }
}
