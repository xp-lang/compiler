<?php namespace net\xp_lang\tests\execution\source;

use lang\FormatException;
use lang\XPClass;

/**
 * Tests class declarations
 *
 */
class ExtensionMethodsTest extends ExecutionTest {

  /**
   * Sets up this test. Add routines verification
   */
  #[@beforeClass]
  public static function useRoutinesVerificationCheck() {
    self::check(new \xp\compiler\checks\RoutinesVerification(), true);
  }

  /**
   * Test extending a class
   *
   */
  #[@test]
  public function classExtension() {
    $class= self::define('class', 'ClassExtension', null, '{
      public static lang.reflect.Method[] methodsNamed(this lang.XPClass $class, text.regex.Pattern $pattern) {
        $r= new lang.reflect.Method[] { };
        foreach ($method in $class.getMethods()) {
          if ($pattern.matches($method.getName())) $r[]= $method;
        }
        return $r;
      }
      
      public lang.reflect.Method runMethod() {
        return self::class.methodsNamed(text.regex.Pattern::compile("run"))[0];
      }
    }');
    $this->assertEquals(
      $class->getMethod('runMethod'), 
      $class->newInstance()->runMethod()
    );
  }

  /**
   * Test extending a primitive
   *
   */
  #[@test]
  public function stringExtension() {
    $class= self::define('class', 'StringExtension', null, '{
      public static bool equal(this string $in, string $cmp, bool $strict) {
        return $strict ? $in === $cmp : $in == $cmp;
      }
      
      public bool run(string $cmp) {
        return "hello".equal($cmp, true);
      }
    }');
    $instance= $class->newInstance();
    $this->assertFalse($instance->run('world'));
    $this->assertTrue($instance->run('hello'));
  }

  /**
   * Test extending an array
   *
   */
  #[@test]
  public function arrayExtension() {
    $class= self::define('class', 'MethodExtension', null, '{
      protected static string[] names(this lang.reflect.Method[] $methods) {
        $r= [];
        foreach ($method in $methods) {
          $r[]= $method.getName();
        }
        return $r;
      }
      
      public bool run(XPClass $class) {
        return $class.getMethods().names();
      }
    }');
    $instance= $class->newInstance();
    $this->assertEquals(
      ['run'],
      $instance->run(XPClass::forName('lang.Runnable'))
    );
  }

  /**
   * Test extending an array
   *
   */
  #[@test]
  public function arrayOfSubclassExtension() {
    $class= self::define('class', 'ObjectExtension', null, '{
      protected static string[] hashCodes(this Object[] $objects) {
        $r= [];
        foreach ($object in $objects) {
          $r[]= $object.hashCode();
        }
        return $r;
      }
      
      public bool run(XPClass[] $classes) {
        return $classes.hashCodes();
      }
    }');
    $instance= $class->newInstance();
    $this->assertEquals(
      [XPClass::forName('lang.Object')->hashCode(), XPClass::forName('lang.Generic')->hashCode()],
      $instance->run([XPClass::forName('lang.Object'), XPClass::forName('lang.Generic')])
    );
  }

  /**
   * Test extending a map
   *
   */
  #[@test]
  public function mapExtension() {
    $class= self::define('class', 'MapExtension', null, '{
      protected static string[] keys(this [:string] $map) {
        $r= [];
        foreach ($key, $value in $map) {
          $r[]= $key;
        }
        return $r;
      }
      
      public bool run([:string] $map) {
        return $map.keys();
      }
    }');
    $instance= $class->newInstance();
    $this->assertEquals(
      ['color', 'name', 'model'],
      $instance->run(['color' => 'black', 'name' => 'Camera', 'model' => '500'])
    );
  }

  /**
   * Test extending a map
   *
   */
  #[@test]
  public function mapOfSubclassExtension() {
    $class= self::define('class', 'ObjectMapExtension', null, '{
      protected static Object[] values(this [:Object] $map) {
        $r= [];
        foreach ($value in $map) {
          $r[]= $value;
        }
        return $r;
      }
      
      public bool run([:XPClass] $map) {
        return $map.values();
      }
    }');
    $instance= $class->newInstance();
    $this->assertEquals(
      [XPClass::forName('lang.Object'), $this->getClass()],
      $instance->run(['object' => XPClass::forName('lang.Object'), 'self' => $this->getClass()])
    );
  }

  /**
   * Test extension methods do not apply if not imported
   *
   */
  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method .+::fieldsNamed/')]
  public function extensionDoesNotApplyIfNotImported() {
    $this->run('return self::class.fieldsNamed(text.regex.Pattern::compile("_.*"));');
  }

  /**
   * Test extension methods must be static
   *
   */
  #[@test, @expect(FormatException::class)]
  public function nonStaticMethod() {
    self::define('class', 'StringIncorrectExtension', null, '{
      public bool equal(this string $in, string $cmp, bool $strict) {
        return $strict ? $in === $cmp : $in == $cmp;
      }
      
      public bool run(string $cmp) {
        return "hello".equal($cmp, true);
      }
    }');
  }
}
