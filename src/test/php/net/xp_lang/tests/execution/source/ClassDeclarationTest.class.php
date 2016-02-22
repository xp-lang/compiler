<?php namespace net\xp_lang\tests\execution\source;

use lang\XPClass;
use lang\Type;
use lang\Primitive;

/**
 * Tests class declarations
 *
 */
class ClassDeclarationTest extends ExecutionTest {
  
  /**
   * Test declaring a class
   *
   */
  #[@test]
  public function echoClass() {
    $class= self::define('class', 'EchoClass', null, '{
      public static string[] echoArgs(string[] $args) {
        return $args;
      }
    }');
    $this->assertEquals('SourceEchoClass', $class->getName());
    $this->assertFalse($class->isInterface());
    $this->assertFalse($class->isEnum());
    
    with ($method= $class->getMethod('echoArgs')); {
      $this->assertEquals('echoArgs', $method->getName());
      $this->assertEquals(MODIFIER_STATIC | MODIFIER_PUBLIC, $method->getModifiers());
      $this->assertEquals(Type::forName('string[]'), $method->getReturnType());
      
      with ($params= $method->getParameters()); {
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(Type::forName('string[]'), $params[0]->getType());
      }
      
      $in= ['Hello', 'World'];
      $this->assertEquals($in, $method->invoke(null, [$in]));
    }
  }

  /**
   * Test declaring a class
   *
   */
  #[@test]
  public function genericClass() {
    $class= self::define('class', 'ListOf<T>', null, '{
      protected T[] $elements;
      
      public __construct(T?... $initial) {
        $this.elements= $initial;
      }
      
      public T add(T? $element) {
        $this.elements[]= $element;
        return $element;
      }
      
      public T[] elements() {
        return $this.elements;
      }
      
      public static void test(string[] $args) {
        $l= new self<string>("Ciao", "Salut");
        foreach ($arg in $args) {
          $l.add($arg);
        }
        return $l;
      }
    }');
    
    $this->assertTrue($class->isGenericDefinition());
    $this->assertEquals(['T'], $class->genericComponents('generic'));
    $this->assertEquals(['params' => 'T', 'return' => 'T'], $class->getMethod('add')->getAnnotation('generic'));
    $this->assertEquals(['return' => 'T[]'], $class->getMethod('elements')->getAnnotation('generic'));
    $this->assertEquals(
      ['Ciao', 'Salut', 'Hello', 'Hallo', 'Hola'],
      $class->getMethod('test')->invoke(null, [['Hello', 'Hallo', 'Hola']])->elements()
    );
  }

  /**
   * Test declaring a class
   *
   */
  #[@test]
  public function genericClassExtendingHashTable() {
    $class= self::define('class', 'MapOf<K, V>', 'util.collections.HashTable<K, V>', '{ }');
    $this->assertEquals(['self' => 'K, V', 'parent' => 'K, V'], $class->getAnnotation('generic'));
  }

  /**
   * Test declaring a class
   *
   */
  #[@test]
  public function genericClassImplementingMap() {
    $class= self::define('abstract class', 'AbstractMapOf<K, V>', 'Object implements util.collections.Map<K, V>', '{ 
      public string hashCode() { }
      public bool equals(Generic? $cmp) { }
    }');
    $this->assertEquals(['self' => 'K, V', 'implements' => ['K, V']], $class->getAnnotation('generic'));
  }

  /**
   * Test declaring a class
   *
   */
  #[@test]
  public function genericInterfaceExtendingMap() {
    $class= self::define('interface', 'IMapOf<K, V>', 'util.collections.Map<K, V>', '{ }');
    $this->assertEquals(['self' => 'K, V', 'extends' => ['K, V']], $class->getAnnotation('generic'));
  }

  /**
   * Test declaring a class
   *
   */
  #[@test]
  public function genericInterfaceExtendingMapPartially() {
    $class= self::define('interface', 'ITypeMapOf<V>', 'util.collections.Map<lang.Type, V>', '{ }');
    $this->assertEquals(['self' => 'V', 'extends' => ['lang.Type, V']], $class->getAnnotation('generic'));
  }

  /**
   * Test declaring a class
   *
   */
  #[@testm]
  public function classInsidePackage() {
    $class= self::define('class', 'ClassInPackage', null, '{ }', ['package demo;']);
    $this->assertEquals('demo.SourceClassInPackage', $class->getName());
    $this->assertEquals('demo\\SourceClassInPackage', literal($class->getName()));
  }

  /**
   * Test declaring a class
   *
   */
  #[@test]
  public function packageClassInsidePackage() {
    $class= self::define('package class', 'PackageClassInPackage', null, '{ }', ['package demo;']);
    $this->assertEquals('demo.SourcePackageClassInPackage', $class->getName());
    $this->assertEquals('demo\\SourcePackageClassInPackage', literal($class->getName()));
  }

  /**
   * Test declaring an interface
   *
   */
  #[@test]
  public function serializableInterface() {
    $class= self::define('interface', 'Paintable', null, '{
      public void paint(Generic $canvas);
    }');
    $this->assertEquals('SourcePaintable', $class->getName());
    $this->assertTrue($class->isInterface());
    $this->assertFalse($class->isEnum());
    
    with ($method= $class->getMethod('paint')); {
      $this->assertEquals('paint', $method->getName());
      $this->assertEquals(MODIFIER_PUBLIC | MODIFIER_ABSTRACT, $method->getModifiers());
      $this->assertEquals(Type::$VOID, $method->getReturnType());
      
      with ($params= $method->getParameters()); {
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(XPClass::forName('lang.Generic'), $params[0]->getType());
      }
    }
  }

  /**
   * Test static initializer block
   *
   */
  #[@test]
  public function staticInitializer() {
    $class= self::define('class', 'StaticInitializer', null, '{
      public static self $instance;
      
      static {
        self::$instance= new self();
      }
    }');
    $this->assertInstanceOf($class, $class->getField('instance')->get(null));
  }

  /**
   * Test class constants
   *
   */
  #[@test]
  public function classConstants() {
    $class= self::define('class', 'ExecutionTestConstants', null, '{
      const int THRESHHOLD= 5;
      const string NAME= "Timm";
      const double TIMEOUT= 0.5;
      const bool PHP= false;
      const var NOTHING = null;
    }');
    
    $this->assertEquals(5, $class->_reflect->getConstant('THRESHHOLD'));
    $this->assertEquals('Timm', $class->_reflect->getConstant('NAME'));
    $this->assertEquals(0.5, $class->_reflect->getConstant('TIMEOUT'));
    $this->assertEquals(false, $class->_reflect->getConstant('PHP'));
    $this->assertEquals(null, $class->_reflect->getConstant('NOTHING'));
  }

  /**
   * Test static member initialization to complex expressions.
   *
   */
  #[@test]
  public function staticMemberInitialization() {
    $class= self::define('class', $this->name, null, '{
      public static XPClass $arrayClass = lang.Type::class;
    }');
    $this->assertInstanceOf(XPClass::class, $class->getField('arrayClass')->get(null));
  }

  /**
   * Test member initialization to complex expressions.
   *
   */
  #[@test]
  public function memberInitialization() {
    $class= self::define('class', $this->name, null, '{
      public lang.Type $type = lang.Type::forName("string");
    }');
    $this->assertEquals(Primitive::$STRING, $class->getField('type')->get($class->newInstance()));
  }

  /**
   * Test member initialization to complex expressions.
   *
   */
  #[@test]
  public function memberInitializationWithParent() {
    $class= self::define('class', $this->name, 'unittest.TestCase', '{
      public lang.Type $type = lang.Type::forName("string");
    }');
    $this->assertEquals(Primitive::$STRING, $class->getField('type')->get($class->newInstance('test')));
  }

  /**
   * Test class annotations
   *
   */
  #[@test]
  public function classAnnotation() {
    $fixture= self::define('class', 'AnnotationsFor'.$this->name, null, '{ }', ['[@fixture]']);
    $this->assertTrue($fixture->hasAnnotation('fixture'));
    $this->assertEquals(null, $fixture->getAnnotation('fixture'));
  }

  /**
   * Test interface annotations
   *
   */
  #[@test]
  public function interfaceAnnotation() {
    $fixture= self::define('interface', 'AnnotationsFor'.$this->name, null, '{ }', ['[@fixture]']);
    $this->assertTrue($fixture->hasAnnotation('fixture'));
    $this->assertEquals(null, $fixture->getAnnotation('fixture'));
  }

  /**
   * Test interface annotations
   *
   */
  #[@test]
  public function enumAnnotation() {
    $fixture= self::define('enum', 'AnnotationsFor'.$this->name, null, '{ }', ['[@fixture]']);
    $this->assertTrue($fixture->hasAnnotation('fixture'));
    $this->assertEquals(null, $fixture->getAnnotation('fixture'));
  }
}
