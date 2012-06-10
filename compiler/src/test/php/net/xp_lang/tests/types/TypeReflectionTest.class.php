<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.types.TypeReflection'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.TypeReflection
   */
  class TypeReflectionTest extends TestCase {
  
    /**
     * Test name() method
     *
     */
    #[@test]
    public function name() {
      $decl= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertEquals('unittest.TestCase', $decl->name());
    }

    /**
     * Test literal() method
     *
     */
    #[@test]
    public function literal() {
      $decl= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertEquals('TestCase', $decl->literal());
    }

    /**
     * Test hasMethod() method
     *
     */
    #[@test]
    public function objectClassHasMethod() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertTrue($decl->hasMethod('equals'), 'equals');
      $this->assertFalse($decl->hasMethod('getName'), 'getName');
    }

    /**
     * Test hasConstructor() method
     *
     */
    #[@test]
    public function objectClassHasNoConstructor() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertFalse($decl->hasConstructor());
    }

    /**
     * Test getConstructor() method
     *
     */
    #[@test]
    public function objectClassNoConstructor() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertNull($decl->getConstructor());
    }

    /**
     * Test hasConstructor() method
     *
     */
    #[@test]
    public function testCaseClassHasConstructor() {
      $decl= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertTrue($decl->hasConstructor());
    }

    /**
     * Test getConstructor() method
     *
     */
    #[@test]
    public function testCaseClassConstructor() {
      $decl= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertInstanceOf('xp.compiler.types.Constructor', $decl->getConstructor());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function classKind() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertEquals(Types::CLASS_KIND, $decl->kind());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function interfaceKind() {
      $decl= new TypeReflection(XPClass::forName('lang.Generic'));
      $this->assertEquals(Types::INTERFACE_KIND, $decl->kind());
    }

    /**
     * Test hasMethod() method for inherited methods
     *
     */
    #[@test]
    public function stringClassHasEqualsMethod() {
      $decl= new TypeReflection(XPClass::forName('lang.types.String'));
      $this->assertTrue($decl->hasMethod('equals'));
    }

    /**
     * Test hasMethod() method for instance methods
     *
     */
    #[@test]
    public function stringClassHasSubstringMethod() {
      $decl= new TypeReflection(XPClass::forName('lang.types.String'));
      $this->assertTrue($decl->hasMethod('substring'));
    }

    /**
     * Test hasMethod() method for nonexistant methods
     *
     */
    #[@test]
    public function stringClassDoesNotHaveGetNameMethod() {
      $decl= new TypeReflection(XPClass::forName('lang.types.String'));
      $this->assertFalse($decl->hasMethod('getName'));
    }

    /**
     * Test getMethod()
     *
     */
    #[@test]
    public function stringClassSubstringMethod() {
      $method= create(new TypeReflection(XPClass::forName('lang.types.String')))->getMethod('substring');
      $this->assertEquals(new TypeName('lang.types.String'), $method->returns);
      $this->assertEquals('substring', $method->name);
      $this->assertEquals(
        array(
          'start'  => array('type' => new TypeName('int'), 'default' => NULL), 
          'length' => array('type' => new TypeName('int'), 'default' => new IntegerNode(0))
        ), 
        $method->parameters
      );
      $this->assertEquals(MODIFIER_PUBLIC, $method->modifiers);
    }

    /**
     * Test hasField() method for instance fields
     *
     */
    #[@test]
    public function stringClassHasLengthField() {
      $decl= new TypeReflection(XPClass::forName('lang.types.String'));
      $this->assertTrue($decl->hasField('length'));
    }

    /**
     * Test hasField() method for nonexistant fields
     *
     */
    #[@test]
    public function stringClassDoesNotHaveCharsField() {
      $decl= new TypeReflection(XPClass::forName('lang.types.String'));
      $this->assertFalse($decl->hasField('chars'));
    }

    /**
     * Test getField()
     *
     */
    #[@test]
    public function stringClassLengthField() {
      $field= create(new TypeReflection(XPClass::forName('lang.types.String')))->getField('length');
      $this->assertEquals(TypeName::$VAR, $field->type);
      $this->assertEquals('length', $field->name);
      $this->assertEquals(MODIFIER_PROTECTED, $field->modifiers);
    }

    /**
     * Test hasIndexer() method
     *
     */
    #[@test]
    public function stringClassHasIndexer() {
      $decl= new TypeReflection(XPClass::forName('lang.types.String'));
      $this->assertTrue($decl->hasIndexer());
    }

    /**
     * Test hasIndexer() method
     *
     */
    #[@test]
    public function objectClassDoesNotHaveIndexer() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertFalse($decl->hasIndexer());
    }

    /**
     * Test getIndexer() method
     *
     */
    #[@test]
    public function stringClassIndexer() {
      $indexer= create(new TypeReflection(XPClass::forName('lang.types.String')))->getIndexer();
      $this->assertEquals(new TypeName('lang.types.Character'), $indexer->type);
      $this->assertEquals(new TypeName('int'), $indexer->parameter);
    }

    /**
     * Test hasConstant() method
     *
     */
    #[@test]
    public function objectClassDoesNotHaveConstant() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertFalse($decl->hasConstant('STATUS_OK'));
    }

    /**
     * Test hasConstant() method
     *
     */
    #[@test]
    public function httpConstantsClassDoesNotHaveConstant() {
      $decl= new TypeReflection(XPClass::forName('peer.http.HttpConstants'));
      $this->assertTrue($decl->hasConstant('STATUS_OK'));
    }

    /**
     * Test hasConstant() method
     *
     */
    #[@test]
    public function httpConstantsConstant() {
      $const= create(new TypeReflection(XPClass::forName('peer.http.HttpConstants')))->getConstant('STATUS_OK');
      $this->assertEquals(new TypeName('int'), $const->type);
      $this->assertEquals(200, $const->value);
    }

    /**
     * Test isEnumerable() method
     *
     */
    #[@test]
    public function objectClassIsNotEnumerable() {
      $decl= new TypeReflection(XPClass::forName('lang.Object'));
      $this->assertFalse($decl->isEnumerable());
    }

    /**
     * Test getEnumerator() method
     *
     */
    #[@test]
    public function arrayListClassEnumerator() {
      $enum= create(new TypeReflection(XPClass::forName('lang.types.ArrayList')))->getEnumerator();
      $this->assertEquals(new TypeName('int'), $enum->key);
      $this->assertEquals(new TypeName('var'), $enum->value);
    }

    /**
     * Test getExtensions() method
     *
     */
    #[@test]
    public function objectClassHasNoExtensionMethods() {
      $this->assertEquals(
        array(), 
        create(new TypeReflection(XPClass::forName('lang.Object')))->getExtensions()
      );
    }

    /**
     * Test getExtensions() method
     *
     */
    #[@test]
    public function extensionMethod() {
      $extensions= create(new TypeReflection(XPClass::forName('net.xp_lang.tests.types.ArraySortingExtensions')))->getExtensions();

      $this->assertEquals(1, sizeof($extensions));
      $this->assertEquals('lang.types.ArrayList', key($extensions));
      $this->assertEquals('sorted', $extensions['lang.types.ArrayList'][0]->name());
    }

    /**
     * Test default arguments reflection
     *
     */
    #[@test]
    public function argumentNullDefault() {
      $fixture= new TypeReflection(ClassLoader::defineClass($this->name.'Fixture', 'lang.Object', array(), '{
        public function fixture($a= NULL) {}
      }'));
      $this->assertEquals(
        new NullNode(),
        $fixture->getMethod('fixture')->parameters['a']['default']
      );
    }

    /**
     * Test default arguments reflection
     *
     */
    #[@test]
    public function argumentStringDefault() {
      $fixture= new TypeReflection(ClassLoader::defineClass($this->name.'Fixture', 'lang.Object', array(), '{
        public function fixture($a= "a") {}
      }'));
      $this->assertEquals(
        new StringNode('a'),
        $fixture->getMethod('fixture')->parameters['a']['default']
      );
    }

    /**
     * Test default arguments reflection
     *
     */
    #[@test]
    public function argumentIntegerDefault() {
      $fixture= new TypeReflection(ClassLoader::defineClass($this->name.'Fixture', 'lang.Object', array(), '{
        public function fixture($a= 0) {}
      }'));
      $this->assertEquals(
        new IntegerNode(0),
        $fixture->getMethod('fixture')->parameters['a']['default']
      );
    }

    /**
     * Test default arguments reflection
     *
     */
    #[@test]
    public function argumentDoubleDefault() {
      $fixture= new TypeReflection(ClassLoader::defineClass($this->name.'Fixture', 'lang.Object', array(), '{
        public function fixture($a= 0.5) {}
      }'));
      $this->assertEquals(
        new DecimalNode(0.5),
        $fixture->getMethod('fixture')->parameters['a']['default']
      );
    }

    /**
     * Test default arguments reflection
     *
     */
    #[@test]
    public function argumentBooleanDefault() {
      $fixture= new TypeReflection(ClassLoader::defineClass($this->name.'Fixture', 'lang.Object', array(), '{
        public function fixture($a= TRUE) {}
      }'));
      $this->assertEquals(
        new BooleanNode(TRUE),
        $fixture->getMethod('fixture')->parameters['a']['default']
      );
    }

    /**
     * Test default arguments reflection
     *
     */
    #[@test]
    public function argumentNonEmptyArrayDefault() {
      $fixture= new TypeReflection(ClassLoader::defineClass($this->name.'Fixture', 'lang.Object', array(), '{
        public function fixture($a= array("a", "b", "c")) {}
      }'));
      $this->assertEquals(
        new ArrayNode(array(
          new StringNode('a'), 
          new StringNode('b'), 
          new StringNode('c')
        )),
        $fixture->getMethod('fixture')->parameters['a']['default']
      );
    }

    /**
     * Test default arguments reflection
     *
     */
    #[@test]
    public function argumentNonEmptyMapDefault() {
      $fixture= new TypeReflection(ClassLoader::defineClass($this->name.'Fixture', 'lang.Object', array(), '{
        public function fixture($a= array("a" => "b", "c" => "d")) {}
      }'));
      $this->assertEquals(
        new MapNode(array(
          array(new StringNode('a'), new StringNode('b')),  // a => b
          array(new StringNode('c'), new StringNode('d')),  // c => d
        )),
        $fixture->getMethod('fixture')->parameters['a']['default']
      );
    }
  }
?>
