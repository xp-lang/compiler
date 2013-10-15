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
  public static function defineFixture() {
    self::$fixture= self::define('class', 'AnnotationsForAnnotationTest', null, '{
      const string TEST = "Test";
    
      [@test]
      public void getAll() { }

      [@test, @value(new lang.types.String("Hello"))]
      public void withNewInstance(var $value) { }

      [@test, @value(self::TEST)]
      public void withClassConstant(var $value) { }

      [@test, @value(lang.CommandLine::$UNIX)]
      public void withEnumMember(var $value) { }
      
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

  public function test_annotation() {
    with ($m= self::$fixture->getMethod('getAll')); {
      $this->assertTrue($m->hasAnnotation('test'));
      $this->assertEquals(null, $m->getAnnotation('test'));
    }
  }

  #[@test]
  public function newinstance_annotation() {
    with ($m= self::$fixture->getMethod('withNewInstance')); {
      $this->assertTrue($m->hasAnnotation('value'));
      $this->assertEquals(new \lang\types\String('Hello'), $m->getAnnotation('value'));
    }
  }

  #[@test]
  public function class_constant_annotation() {
    with ($m= self::$fixture->getMethod('withClassConstant')); {
      $this->assertTrue($m->hasAnnotation('value'));
      $this->assertEquals('Test', $m->getAnnotation('value'));
    }
  }

  #[@test]
  public function enum_member_annotation() {
    with ($m= self::$fixture->getMethod('withEnumMember')); {
      $this->assertTrue($m->hasAnnotation('value'));
      $this->assertEquals(\lang\CommandLine::$UNIX, $m->getAnnotation('value'));
    }
  }

  #[@test]
  public function ignore_annotation() {
    with ($m= self::$fixture->getMethod('deleteAll')); {
      $this->assertTrue($m->hasAnnotation('test'), '@test');
      $this->assertEquals(null, $m->getAnnotation('test'), '@test');
      $this->assertTrue($m->hasAnnotation('ignore'), '@ignore');
      $this->assertEquals('Risky', $m->getAnnotation('ignore'), '@ignore');
    }
  }

  #[@test]
  public function limit_annotation() {
    with ($m= self::$fixture->getMethod('updateAll')); {
      $this->assertTrue($m->hasAnnotation('test'), '@test');
      $this->assertEquals(null, $m->getAnnotation('test'), '@test');
      $this->assertTrue($m->hasAnnotation('limit'), '@limit');
      $this->assertEquals(array('time' => 0.1), $m->getAnnotation('limit'), '@limit');
    }
  }

  #[@test]
  public function restricted_annotation() {
    with ($m= self::$fixture->getMethod('reset')); {
      $this->assertTrue($m->hasAnnotation('restricted'));
      $this->assertEquals(array('roles' => array('admin', 'root')), $m->getAnnotation('restricted'));
    }
  }
}
