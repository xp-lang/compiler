<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests annotations
 *
 */
class AnnotationTest extends ExecutionTest {
  protected static $fixture= null;
  
  /**
   * Sets up test case and define class to be used in fixtures
   */
  #[@beforeClass]
  public function defineFixture() {
    self::$fixture= self::define('class', 'AnnotationsForAnnotationTest', null, '{
    
      [@test]
      public void getAll() { }
      
      [@test, @ignore("Risky")]
      public void deleteAll() { }

      [@test, @limit(time = 0.1)]
      public void updateAll() { }

      // TODO: Support this grammatically
      //
      // [@test, @expect(lang.FormatException::class)]
      // public void findBy() { }

      [@restricted(roles = ["admin", "root"])]
      public void reset() { }
    }');
  }

  /**
   * Test simple annotation
   *
   */
  #[@test]
  public function testAnnotation() {
    with ($m= self::$fixture->getMethod('getAll')); {
      $this->assertTrue($m->hasAnnotation('test'));
      $this->assertEquals(null, $m->getAnnotation('test'));
    }
  }

  /**
   * Test multiple annotations
   *
   */
  #[@test]
  public function ignoreAnnotation() {
    with ($m= self::$fixture->getMethod('deleteAll')); {
      $this->assertTrue($m->hasAnnotation('test'), '@test');
      $this->assertEquals(null, $m->getAnnotation('test'), '@test');
      $this->assertTrue($m->hasAnnotation('ignore'), '@ignore');
      $this->assertEquals('Risky', $m->getAnnotation('ignore'), '@ignore');
    }
  }

  /**
   * Test multiple annotations
   *
   */
  #[@test]
  public function limitAnnotation() {
    with ($m= self::$fixture->getMethod('updateAll')); {
      $this->assertTrue($m->hasAnnotation('test'), '@test');
      $this->assertEquals(null, $m->getAnnotation('test'), '@test');
      $this->assertTrue($m->hasAnnotation('limit'), '@limit');
      $this->assertEquals(array('time' => 0.1), $m->getAnnotation('limit'), '@limit');
    }
  }

  /**
   * Test annotation with array value
   *
   */
  #[@test]
  public function restrictedAnnotation() {
    with ($m= self::$fixture->getMethod('reset')); {
      $this->assertTrue($m->hasAnnotation('restricted'));
      $this->assertEquals(array('roles' => array('admin', 'root')), $m->getAnnotation('restricted'));
    }
  }
}
