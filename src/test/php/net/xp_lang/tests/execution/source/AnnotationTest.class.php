<?php namespace net\xp_lang\tests\execution\source;

use net\xp_lang\tests\StringBuffer;

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
      new StringBuffer('Hello'),
      $this->methodAnnotatedWith('[@value(new net.xp_lang.tests.StringBuffer("Hello"))]')->getAnnotation('value')
    );
  }

  #[@test]
  public function newinstance_annotation_with_array() {
    $this->assertEquals(
      array(new StringBuffer('Hello')),
      $this->methodAnnotatedWith('[@value([new net.xp_lang.tests.StringBuffer("Hello")])]')->getAnnotation('value')
    );
  }

  #[@test]
  public function newinstance_annotation_with_map() {
    $this->assertEquals(
      array('hello' => new StringBuffer('Hello')),
      $this->methodAnnotatedWith('[@value([hello : new net.xp_lang.tests.StringBuffer("Hello")])]')->getAnnotation('value')
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
  public function has_ignore_annotation() {
    $this->assertTrue(
      $this->methodAnnotatedWith('[@ignore("Risky")]')->hasAnnotation('ignore')
    );
  }

  #[@test]
  public function ignore_annotation() {
    $this->assertEquals(
      'Risky',
      $this->methodAnnotatedWith('[@ignore("Risky")]')->getAnnotation('ignore')
    );
  }

  #[@test]
  public function has_limit_annotation() {
    $this->assertTrue(
      $this->methodAnnotatedWith('[@test, @limit(time = 0.1)]')->hasAnnotation('limit')
    );
  }

  #[@test]
  public function limit_annotation() {
    $this->assertEquals(
      array('time' => 0.1),
      $this->methodAnnotatedWith('[@test, @limit(time = 0.1)]')->getAnnotation('limit')
    );
  }

  #[@test]
  public function has_restricted_annotation() {
    $this->assertTrue(
      $this->methodAnnotatedWith('[@restricted(roles = ["admin", "root"])]')->hasAnnotation('restricted')
    );
  }

  #[@test]
  public function restricted_annotation() {
    $this->assertEquals(
      array('roles' => array('admin', 'root')),
      $this->methodAnnotatedWith('[@restricted(roles = ["admin", "root"])]')->getAnnotation('restricted')
    );
  }
}
