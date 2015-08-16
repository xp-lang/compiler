<?php namespace net\xp_lang\tests\execution\source;

use lang\XPClass;
use lang\Primitive;

/**
 * Tests class instance creation
 *
 */
class InstanceCreationTest extends ExecutionTest {

  /**
   * Assert a given instance is an anonymous instance
   *
   * @param   string name
   * @param   lang.Generic instance
   * @throws  unittest.AssertionFailedError
   */
  protected function assertAnonymousInstanceOf($name, \lang\Generic $instance) {
    $this->assertInstanceOf($name, $instance);
    $this->assertTrue((bool)strstr($instance->getClassName(), '··'), $instance->getClassName());
  }
  
  #[@test]
  public function new_instance_from_object_class() {
    $this->assertInstanceOf('lang.Object', $this->run('return new Object();'));
  }

  #[@test]
  public function new_instance_from_fully_qualified_object_class() {
    $this->assertInstanceOf('lang.Object', $this->run('return new lang.Object();'));
  }

  #[@test]
  public function new_instance_from_namespaced_class() {
    $this->assertInstanceOf('xp.compiler.Syntax', $this->run('return xp.compiler.Syntax::forName("xp");'));
  }

  #[@test]
  public function new_generic_hashtable() {
    $hash= $this->run('return new util.collections.HashTable<string, lang.Generic>();');
    $this->assertEquals(
      array(Primitive::$STRING, XPClass::forName('lang.Generic')), 
      $hash->getClass()->genericArguments()
    );
  }

  #[@test]
  public function new_generic_vector() {
    $hash= $this->run('return new util.collections.Vector<int>();');
    $this->assertEquals(
      array(Primitive::$INT), 
      $hash->getClass()->genericArguments()
    );
  }
  
  #[@test]
  public function anonymous_interface_instance() {
    $runnable= $this->run('return new lang.Runnable() {
      public void run() {
        throw new lang.MethodNotImplementedException("run");
      }
    };');
    $this->assertAnonymousInstanceOf('lang.Runnable', $runnable);
  }

  #[@test]
  public function anonymous_instance() {
    $object= $this->run('return new lang.Object() { };');
    $this->assertAnonymousInstanceOf('lang.Object', $object);
  }

  #[@test]
  public function anonymous_instance_with_body() {
    $object= $this->run('return new lang.Object() {
      public void run() {
        throw new lang.MethodNotImplementedException("run");
      }
    };');
    $this->assertAnonymousInstanceOf('lang.Object', $object);
  }

  #[@test]
  public function anonymous_interface_instance_inside_test_package() {
    $runnable= $this->run('return new lang.Runnable() {
      public void run() {
        throw new lang.MethodNotImplementedException("run");
      }
    };', array('package test;'));
    $this->assertAnonymousInstanceOf('lang.Runnable', $runnable);
  }

  #[@test]
  public function two_anonymous_interface_instances() {
    $instances= $this->run('return [
      new Object() { public string id() -> "a"; },
      new Object() { public string id() -> "b"; }
    ];');

    $this->assertEquals(
      array('a', 'b'),
      array_map(function($e) { return $e->id(); }, $instances)
    );
  }

  #[@test]
  public function anonymous_instance_from_abstract_base_class() {
    $command= $this->run('return new util.cmd.Command() {
      public void run() {
        throw new lang.MethodNotImplementedException("run");
      }
    };');
    $this->assertAnonymousInstanceOf('util.cmd.Command', $command);
  }

  #[@test]
  public function anonymous_generic_interface_instance() {
    $f= $this->run('return new net.xp_lang.tests.execution.source.Filter<string>() {
      public bool accept(string $e) {
        return "Test" === $e;
      }
    };');
    $this->assertAnonymousInstanceOf('net.xp_lang.tests.execution.source.Filter', $f);
    $this->assertTrue(
      $f->getClass()->isGeneric(), 
      'generic'
    );
    $this->assertEquals(
      XPClass::forName('net.xp_lang.tests.execution.source.Filter'), 
      $f->getClass()->genericDefinition()
    );
    $this->assertEquals(
      array(Primitive::$STRING), 
      $f->getClass()->genericArguments()
    );
  }
}
