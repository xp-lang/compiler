<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests arrays
 *
 */
class CatchTest extends ExecutionTest {
  
  /**
   * Test try ... catch
   *
   */
  #[@test]
  public function catchNoException() {
    $this->assertEquals(['Try'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
      } catch (FormatException $e) {
        $r[]= "Catch";
      }
      return $r;
    '));
  }

  /**
   * Test try ... catch
   *
   */
  #[@test]
  public function catchWithException() {
    $this->assertEquals(['Try', 'Catch'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
        throw new FormatException("Error");
      } catch (FormatException $e) {
        $r[]= "Catch";
      }
      return $r;
    '));
  }

  /**
   * Test try ... catch
   *
   */
  #[@test]
  public function catchSubclass() {
    $this->assertEquals(['Try', 'Catch'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
        throw new FormatException("Error");
      } catch (Throwable $e) {
        $r[]= "Catch";
      }
      return $r;
    '));
  }

  /**
   * Test try ... catch ... catch
   *
   */
  #[@test]
  public function catchIAE() {
    $this->assertEquals(['Try', 'Catch.IAE'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
        throw new IllegalArgumentException("Error");
      } catch (IllegalArgumentException $e) {
        $r[]= "Catch.IAE";
      } catch (FormatException $e) {
        $r[]= "Catch.FE";
      }
      return $r;
    '));
  }

  /**
   * Test try ... catch ... catch
   *
   */
  #[@test]
  public function catchFE() {
    $this->assertEquals(['Try', 'Catch.FE'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
        throw new FormatException("Error");
      } catch (IllegalArgumentException $e) {
        $r[]= "Catch.IAE";
      } catch (FormatException $e) {
        $r[]= "Catch.FE";
      }
      return $r;
    '));
  }

  /**
   * Test try ... catch (A|B)
   *
   */
  #[@test]
  public function catchMultipleFE() {
    $this->assertEquals(['Try', 'Catch'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
        throw new FormatException("Error");
      } catch (IllegalArgumentException | FormatException $e) {
        $r[]= "Catch";
      }
      return $r;
    '));
  }

  /**
   * Test try ... catch (A|B) when B is thrown
   *
   */
  #[@test]
  public function catchMultipleIAE() {
    $this->assertEquals(['Try', 'Catch'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
        throw new IllegalArgumentException("Error");
      } catch (IllegalArgumentException | FormatException $e) {
        $r[]= "Catch";
      }
      return $r;
    '));
  }

  /**
   * Test try ... catch (A|B) when neither A nor B is thrown
   *
   */
  #[@test]
  public function catchMultipleISE() {
    $this->assertEquals(['Try', 'Catch.ISE'], $this->run('
      $r= [];
      try {
        $r[]= "Try";
        throw new IllegalStateException("Error");
      } catch (IllegalArgumentException | FormatException $e) {
        $r[]= "Catch";
      } catch (IllegalStateException $e) {
        $r[]= "Catch.ISE";
      }
      return $r;
    '));
  }
}