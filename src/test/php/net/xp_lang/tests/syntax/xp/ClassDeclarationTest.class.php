<?php namespace net\xp_lang\tests\syntax\xp;

use text\parser\generic\ParseException;
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
    return (new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration;
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
        [],                    // Implements
        []                     // Body
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
        [],                    // Implements
        [
          new StaticInitializerNode([
          ])
        ]
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
        [new AnnotationNode([
          'type'       => 'Deprecated'
        ])],
        new TypeName('Empty'),
        null,
        [],
        []
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
        [],
        []
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
        [new TypeName('Traceable')],
        []
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
        [new TypeName('util.Observer'), new TypeName('Traceable')],
        []
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
        [new TypeName('Observer')],
        []
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
        [],
        []
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
        [],
        []
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
        new TypeName('Class', [new TypeName('T')]),
        null,
        [],
        []
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
        new TypeName('HashTable', [new TypeName('K'), new TypeName('V')]),
        null,
        [new TypeName('Map', [new TypeName('K'), new TypeName('V')])],
        []
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
        []
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
        new TypeName('Filter', [new TypeName('T')]),
        null,
        []
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
        new TypeName('Map', [new TypeName('K'), new TypeName('V')]),
        null,
        []
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
        [new TypeName('util.log.Traceable')],
        []
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
        [new TypeName('Traceable'), new TypeName('Observer', [new TypeName('T')])],
        []
      ), 
      $this->parse('interface Debuggable extends Traceable, Observer<T> { }')
    );
  }

  /**
   * Test array type cannot be used as class name
   *
   */
  #[@test, @expect(ParseException::class)]
  public function noArrayTypeAsClassName() {
    $this->parse('class int[] { }');
  }

  /**
   * Test array type cannot be used as enum name
   *
   */
  #[@test, @expect(ParseException::class)]
  public function noArrayTypeAsEnumName() {
    $this->parse('enum int[] { }');
  }

  /**
   * Test array type cannot be used as interface name
   *
   */
  #[@test, @expect(ParseException::class)]
  public function noArrayTypeAsInterfaceName() {
    $this->parse('interface int[] { }');
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function methodAndField() {
    $this->assertEquals([new FieldNode([
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => new TypeName('self'),
      'initialization'  => new NullNode()
    ]), new MethodNode([
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations' => null,
      'name'        => 'getInstance',
      'returns'     => new TypeName('self'),
      'parameters'  => null, 
      'throws'      => null,
      'body'        => [],
      'extension'   => null
    ])], $this->parse('class Logger { 
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
    $this->assertEquals([new MethodNode([
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations' => null,
      'name'        => 'getInstance',
      'returns'     => new TypeName('self'),
      'parameters'  => null, 
      'throws'      => null,
      'body'        => [],
      'extension'   => null
    ]), new FieldNode([
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => new TypeName('self'),
      'initialization'  => new NullNode()
    ])], $this->parse('class Logger { 
      public static self getInstance() { /* ... */ }
      private static self $instance= null;
    }')->body);
  }
}
