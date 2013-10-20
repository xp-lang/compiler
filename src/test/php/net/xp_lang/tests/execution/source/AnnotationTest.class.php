<?php namespace net\xp_lang\tests\execution\source;

/**
 * Tests annotations
 *
 */
class AnnotationTest extends ExecutionTest {
  protected static $fixture= null;

  /**
   * Returns a method object
   *
   * @param  string $source source of annotation
   * @return lang.reflect.Method
   */
  protected function methodAnnotatedWith($source) {
    $class= self::define('class', 'AnnotationTest_'.$this->name, null, '{
      const string TEST = "Test";
      '.$source.' public void fixture() { }
    }');
    return $class->getMethod('fixture');
  }

  public function test_annotation_exists() {
    $this->assertTrue($this->methodAnnotatedWith('[@test]')->hasAnnotation('test'));
  }

  public function test_annotation() {
    $this->assertEquals(null, $this->methodAnnotatedWith('[@test]')->getAnnotation('test'));
  }

  #[@test]
  public function newinstance_annotation() {
    $this->assertEquals(
      new \lang\types\String('Hello'),
      $this->methodAnnotatedWith('[@value(new lang.types.String("Hello"))]')->getAnnotation('value')
    );
  }

  #[@test]
  public function newinstance_annotation_with_array() {
    $this->assertEquals(
      array(new \lang\types\String('Hello')),
      $this->methodAnnotatedWith('[@value([new lang.types.String("Hello")])]')->getAnnotation('value')
    );
  }

  #[@test]
  public function newinstance_annotation_with_map() {
    $this->assertEquals(
      array('hello' => new \lang\types\String('Hello')),
      $this->methodAnnotatedWith('[@value([hello : new lang.types.String("Hello")])]')->getAnnotation('value')
    );
  }

  #[@test]
  public function class_constant_annotation_via_self() {
    $this->assertEquals(
      'Test',
      $this->methodAnnotatedWith('[@value(self::TEST)]')->getAnnotation('value')
    );
  }

  #[@test]
  public function enum_member_annotation() {
    $this->assertEquals(
      \lang\CommandLine::$UNIX,
      $this->methodAnnotatedWith('[@value(lang.CommandLine::$UNIX)]')->getAnnotation('value')
    );
  }

  #[@test]
  public function ignore_annotation() {
    with ($m= $this->methodAnnotatedWith('[@test, @ignore("Risky")]')); {
      $this->assertTrue($m->hasAnnotation('test'), '@test');
      $this->assertEquals(null, $m->getAnnotation('test'), '@test');
      $this->assertTrue($m->hasAnnotation('ignore'), '@ignore');
      $this->assertEquals('Risky', $m->getAnnotation('ignore'), '@ignore');
    }
  }

  #[@test]
  public function limit_annotation() {
    with ($m= $this->methodAnnotatedWith('[@test, @limit(time = 0.1)]')); {
      $this->assertTrue($m->hasAnnotation('test'), '@test');
      $this->assertEquals(null, $m->getAnnotation('test'), '@test');
      $this->assertTrue($m->hasAnnotation('limit'), '@limit');
      $this->assertEquals(array('time' => 0.1), $m->getAnnotation('limit'), '@limit');
    }
  }

  #[@test]
  public function restricted_annotation() {
    with ($m= $this->methodAnnotatedWith('[@restricted(roles = ["admin", "root"])]')); {
      $this->assertTrue($m->hasAnnotation('restricted'));
      $this->assertEquals(array('roles' => array('admin', 'root')), $m->getAnnotation('restricted'));
    }
  }
}
