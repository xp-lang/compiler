<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\StaticInitializerNode;
use xp\compiler\ast\AnnotationNode;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\InterfaceNode;
use xp\compiler\ast\NullNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class ClassDeclarationTest extends ParserTestCase {

  /**
   * Parse method source and return statements inside this method.
   *
   * @param   string src
   * @return  xp.compiler.Node
   */
  protected function parse($src) {
    return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration;
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function emtpyClass() {
    $this->assertEquals(
      new ClassNode(
        0,                          // Modifiers
        null,                       // Annotations
        new TypeName('Empty'),      // Name
        null,                       // Parent
        array(),                    // Implements
        null                        // Body
      ), 
      $this->parse('class Empty { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function classWithStaticInitializer() {
    $this->assertEquals(
      new ClassNode(
        0,                          // Modifiers
        null,                       // Annotations
        new TypeName('Driver'),     // Name
        null,                       // Parent
        array(),                    // Implements
        array(
          new StaticInitializerNode(array(
          ))
        )
      ), 
      $this->parse('class Driver { static { } }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function annotatedClass() {
    $this->assertEquals(
      new ClassNode(
        0,
        array(new AnnotationNode(array(
          'type'       => 'Deprecated'
        ))),
        new TypeName('Empty'),
        null,
        array(),
        null
      ), 
      $this->parse('[@Deprecated] class Empty { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function classWithParentClass() {
    $this->assertEquals(
      new ClassNode(
        0,
        null,
        new TypeName('Class'),
        new TypeName('lang.Object'),
        array(),
        null
      ), 
      $this->parse('class Class extends lang.Object { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function classWithInterface() {
    $this->assertEquals(
      new ClassNode(
        0,
        null,
        new TypeName('HttpConnection'),
        null,
        array(new TypeName('Traceable')),
        null
      ), 
      $this->parse('class HttpConnection implements Traceable { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function classWithInterfaces() {
    $this->assertEquals(
      new ClassNode(
        0,
        null,
        new TypeName('Math'),
        null,
        array(new TypeName('util.Observer'), new TypeName('Traceable')),
        null
      ), 
      $this->parse('class Math implements util.Observer, Traceable { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function classWithParentClassAndInterface() {
    $this->assertEquals(
      new ClassNode(
        0,
        null,
        new TypeName('Integer'),
        new TypeName('Number'),
        array(new TypeName('Observer')),
        null
      ), 
      $this->parse('class Integer extends Number implements Observer { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function publicClass() {
    $this->assertEquals(
      new ClassNode(
        MODIFIER_PUBLIC,
        null,
        new TypeName('Class'),
        null,
        array(),
        null
      ), 
      $this->parse('public class Class { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function abstractClass() {
    $this->assertEquals(
      new ClassNode(
        MODIFIER_PUBLIC | MODIFIER_ABSTRACT,
        null,
        new TypeName('Base'),
        null,
        array(),
        null
      ), 
      $this->parse('public abstract class Base { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function genericClass() {
    $this->assertEquals(
      new ClassNode(
        0,
        null,
        new TypeName('Class', array(new TypeName('T'))),
        null,
        array(),
        null
      ), 
      $this->parse('class Class<T> { }')
    );
  }

  /**
   * Test class declaration
   *
   */
  #[@test]
  public function hashTableClass() {
    $this->assertEquals(
      new ClassNode(
        0,
        null,
        new TypeName('HashTable', array(new TypeName('K'), new TypeName('V'))),
        null,
        array(new TypeName('Map', array(new TypeName('K'), new TypeName('V')))),
        null
      ), 
      $this->parse('class HashTable<K, V> implements Map<K, V> { }')
    );
  }

  /**
   * Test interface declaration
   *
   */
  #[@test]
  public function emtpyInterface() {
    $this->assertEquals(
      new InterfaceNode(
        0,
        null,
        new TypeName('Empty'),
        null,
        null
      ), 
      $this->parse('interface Empty { }')
    );
  }

  /**
   * Test interface declaration
   *
   */
  #[@test]
  public function genericInterface() {
    $this->assertEquals(
      new InterfaceNode(
        0,
        null,
        new TypeName('Filter', array(new TypeName('T'))),
        null,
        null
      ), 
      $this->parse('interface Filter<T> { }')
    );
  }

  /**
   * Test interface declaration
   *
   */
  #[@test]
  public function twoComponentGenericInterface() {
    $this->assertEquals(
      new InterfaceNode(
        0,
        null,
        new TypeName('Map', array(new TypeName('K'), new TypeName('V'))),
        null,
        null
      ), 
      $this->parse('interface Map<K, V> { }')
    );
  }

  /**
   * Test interface declaration
   *
   */
  #[@test]
  public function interfaceWithParent() {
    $this->assertEquals(
      new InterfaceNode(
        0,
        null,
        new TypeName('Debuggable'),
        array(new TypeName('util.log.Traceable')),
        null
      ), 
      $this->parse('interface Debuggable extends util.log.Traceable { }')
    );
  }

  /**
   * Test interface declaration
   *
   */
  #[@test]
  public function interfaceWithParents() {
    $this->assertEquals(
      new InterfaceNode(
        0,
        null,
        new TypeName('Debuggable'),
        array(new TypeName('Traceable'), new TypeName('Observer', array(new TypeName('T')))),
        null
      ), 
      $this->parse('interface Debuggable extends Traceable, Observer<T> { }')
    );
  }

  /**
   * Test array type cannot be used as class name
   *
   */
  #[@test, @expect('text.parser.generic.ParseException')]
  public function noArrayTypeAsClassName() {
    $this->parse('class int[] { }');
  }

  /**
   * Test array type cannot be used as enum name
   *
   */
  #[@test, @expect('text.parser.generic.ParseException')]
  public function noArrayTypeAsEnumName() {
    $this->parse('enum int[] { }');
  }

  /**
   * Test array type cannot be used as interface name
   *
   */
  #[@test, @expect('text.parser.generic.ParseException')]
  public function noArrayTypeAsInterfaceName() {
    $this->parse('interface int[] { }');
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function methodAndField() {
    $this->assertEquals(array(new FieldNode(array(
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => new TypeName('self'),
      'initialization'  => new NullNode()
    )), new MethodNode(array(
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations' => null,
      'name'        => 'getInstance',
      'returns'     => new TypeName('self'),
      'parameters'  => null, 
      'throws'      => null,
      'body'        => array(),
      'extension'   => null
    ))), $this->parse('class Logger { 
      private static self $instance= null;
      public static self getInstance() { /* ... */ }
    }')->body);
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function fieldAndMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations' => null,
      'name'        => 'getInstance',
      'returns'     => new TypeName('self'),
      'parameters'  => null, 
      'throws'      => null,
      'body'        => array(),
      'extension'   => null
    )), new FieldNode(array(
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => new TypeName('self'),
      'initialization'  => new NullNode()
    ))), $this->parse('class Logger { 
      public static self getInstance() { /* ... */ }
      private static self $instance= null;
    }')->body);
  }
}
