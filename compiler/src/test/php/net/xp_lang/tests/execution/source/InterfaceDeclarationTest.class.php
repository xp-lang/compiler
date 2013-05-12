<?php namespace net\xp_lang\tests\execution\source;

use lang\Primitive;
use lang\XPClass;

/**
 * Tests interface declarations
 *
 */
class InterfaceDeclarationTest extends ExecutionTest {

  /**
   * Sets up this test. Add routines, fields and properties verification
   */
  #[@beforeClass]
  public static function useRoutinesVerificationCheck() {
    self::check(new \xp\compiler\checks\RoutinesVerification(), true);
    self::check(new \xp\compiler\checks\FieldsVerification(), true);
    self::check(new \xp\compiler\checks\PropertiesVerification(), true);
  }
  
  /**
   * Test declaring an interface
   *
   */
  #[@test]
  public function comparableInterface() {
    $class= $this->define('interface', 'Comparable', null, '{
      public int compareTo(Generic $in);
    }');
    $this->assertEquals('SourceComparable', $class->getName());
    $this->assertTrue($class->isInterface());
    
    with ($method= $class->getMethod('compareTo')); {
      $this->assertEquals('compareTo', $method->getName());
      $this->assertEquals(MODIFIER_PUBLIC | MODIFIER_ABSTRACT, $method->getModifiers());
      $this->assertEquals(Primitive::$INTEGER, $method->getReturnType());
      
      with ($params= $method->getParameters()); {
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(XPClass::forName('lang.Generic'), $params[0]->getType());
      }
    }
  }

  /**
   * Test declaring an interface with fields.
   *
   * TODO: This should throw a CompilationException
   */
  #[@test, @expect('lang.FormatException')]
  public function interfacesMayNotHaveFields() {
    $this->define('interface', 'WithField', null, '{
      public int $field = 0;
    }');
  }

  /**
   * Test declaring an interface with properties.
   *
   * TODO: This should throw a CompilationException
   */
  #[@test, @expect('lang.FormatException')]
  public function interfacesMayNotHaveProperties() {
    $this->define('interface', 'WithProperty', null, '{
      public int property { get { return 0; } }
    }');
  }

  /**
   * Test declaring a method inside an interface with body
   *
   * TODO: This should throw a CompilationException
   */
  #[@test, @expect('lang.FormatException')]
  public function interfaceMethodsMayNotContainBody() {
    $this->define('interface', 'WithMethod', null, '{
      public int method() {
        return 0;
      }
    }');
  }

  /**
   * Test a generic interface declaration
   *
   */
  #[@test]
  public function genericInterface() {
    $class= $this->define('interface', 'Filter<T>', null, '{ 
      public bool accept(T $element);
    }');
    $this->assertTrue($class->isGenericDefinition());
    $this->assertEquals(array('T'), $class->genericComponents());

    $this->assertEquals(array('params' => 'T'), $class->getMethod('accept')->getAnnotation('generic'));
  }
}
