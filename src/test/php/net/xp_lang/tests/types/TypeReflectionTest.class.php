<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\TypeReflection;
use xp\compiler\types\TypeName;
use xp\compiler\types\Parameter;
use xp\compiler\types\Types;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\NullNode;
use lang\XPClass;
use lang\ClassLoader;

/**
 * TestCase
 *
 * @see      xp://xp.compiler.types.TypeReflection
 */
class TypeReflectionTest extends \unittest\TestCase {

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
        new Parameter('start', new TypeName('int')),
        new Parameter('length', new TypeName('int'), new IntegerNode(0))
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
   * Test "self" used in return type
   *
   */
  #[@test]
  public function selfReturnType() {
    $builder= create(new TypeReflection(XPClass::forName('net.xp_lang.tests.types.Builder')));
    $this->assertEquals(
      new TypeName('net.xp_lang.tests.types.Builder'),
      $builder->getMethod('create')->returns
    );
  }

  #[@test]
  public function selfParameterType() {
    $builder= create(new TypeReflection(XPClass::forName('net.xp_lang.tests.types.Builder')));
    $this->assertEquals(
      new Parameter('self', new TypeName('net.xp_lang.tests.types.Builder'), new NullNode()),
      $builder->getMethod('create')->parameters[0]
    );
  }

  #[@test]
  public function parameter_with_array_default() {
    $cl= ClassLoader::defineClass('TypeReflectionTest_'.$this->name, 'lang.Object', array(), '{
      /** @param string[] param */
      public function fixture($param= array()) { }
    }');
    $this->assertEquals(
      new Parameter('param', new TypeName('string[]'), new \xp\compiler\ast\ArrayNode(array(
        'type'   => new TypeName('string[]'),
        'values' => array()
      ))),
      create(new TypeReflection($cl))->getMethod('fixture')->parameters[0]
    );
  }

  #[@test]
  public function parameter_with_map_default() {
    $cl= ClassLoader::defineClass('TypeReflectionTest_'.$this->name, 'lang.Object', array(), '{
      /** @param [:string] param */
      public function fixture($param= array()) { }
    }');
    $this->assertEquals(
      new Parameter('param', new TypeName('[:string]'), new \xp\compiler\ast\MapNode(array(
        'type'     => new TypeName('[:string]'),
        'elements' => array()
      ))),
      create(new TypeReflection($cl))->getMethod('fixture')->parameters[0]
    );
  }

  #[@test]
  public function enumMemberType() {
    $cl= ClassLoader::defineClass('TypeReflectionTest_Enum', 'lang.Enum', array(), '{
      public static $a= 0, $b;
    }');
    $this->assertEquals(
      new TypeName($cl->getField('b')->get(null)->getClassName()),
      create(new TypeReflection($cl))->getField('b')->type
    );
  }

  #[@test]
  public function abstractEnumMemberType() {
    $cl= ClassLoader::defineClass('TypeReflectionTest_AbstractEnum', 'lang.Enum', array(), '{
      public static $a, $b;

      static function __static() {
        self::$a= newinstance(__CLASS__, array(0, "a"), "{
          static function __static() { }
          public function code() { return 97; }
        }");
        self::$a= newinstance(__CLASS__, array(1, "b"), "{
          static function __static() { }
          public function code() { return 98; }
        }");
      }
    }');
    $this->assertEquals(
      new TypeName($cl->getName()),
      create(new TypeReflection($cl))->getField('a')->type
    );
  }
}