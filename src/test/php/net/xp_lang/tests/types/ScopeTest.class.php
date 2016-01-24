<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\ResolveException;
use xp\compiler\emit\php\V54Emitter;
use xp\compiler\types\TypeReflection;
use xp\compiler\types\TypeReference;
use xp\compiler\types\ArrayTypeOf;
use xp\compiler\types\MapTypeOf;
use xp\compiler\types\GenericType;
use xp\compiler\types\TaskScope;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use xp\compiler\types\Method;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\MapNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\HexNode;
use xp\compiler\ast\OctalNode;
use xp\compiler\ast\DecimalNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\ComparisonNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\task\CompilationTask;
use xp\compiler\Syntax;
use io\File;
use lang\XPClass;

class ScopeTest extends \unittest\TestCase {
  private $fixture;
  
  /**
   * Sets up this testcase
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new TaskScope(new CompilationTask(
      new FileSource(new File(__FILE__), Syntax::forName('xp')),
      new NullDiagnosticListener(),
      new FileManager(),
      new V54Emitter()
    ));
  }
  
  #[@test]
  public function arrayType() {
    $this->assertEquals(new TypeName('var[]'), $this->fixture->typeOf(new ArrayNode()));
  }

  #[@test]
  public function typedArrayType() {
    $this->assertEquals(new TypeName('string[]'), $this->fixture->typeOf(new ArrayNode([
      'values'        => null,
      'type'          => new TypeName('string[]'),
    ])));
  }
  
  #[@test]
  public function mapType() {
    $this->assertEquals(new TypeName('[:var]'), $this->fixture->typeOf(new MapNode()));
  }

  #[@test]
  public function typedMapType() {
    $this->assertEquals(new TypeName('[:string]'), $this->fixture->typeOf(new MapNode([
      'elements'      => null,
      'type'          => new TypeName('[:string]'),
    ])));
  }
  
  #[@test]
  public function stringType() {
    $this->assertEquals(new TypeName('string'), $this->fixture->typeOf(new StringNode('')));
  }
  
  #[@test]
  public function intType() {
    $this->assertEquals(new TypeName('int'), $this->fixture->typeOf(new IntegerNode('')));
  }
  
  #[@test]
  public function hexType() {
    $this->assertEquals(new TypeName('int'), $this->fixture->typeOf(new HexNode('')));
  }

  #[@test]
  public function octalType() {
    $this->assertEquals(new TypeName('int'), $this->fixture->typeOf(new OctalNode('')));
  }

  #[@test]
  public function decimalType() {
    $this->assertEquals(new TypeName('double'), $this->fixture->typeOf(new DecimalNode('')));
  }
  
  #[@test]
  public function nullType() {
    $this->assertEquals(new TypeName('lang.Object'), $this->fixture->typeOf(new NullNode()));
  }
  
  #[@test]
  public function boolType() {
    $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new BooleanNode(true)));
  }
  
  #[@test]
  public function typeOfAComparison() {
    $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new ComparisonNode()));
  }

  #[@test]
  public function typeOfBracedExpressionNode() {
    $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new BracedExpressionNode(new BooleanNode(true))));
    $this->assertEquals(new TypeName('string'), $this->fixture->typeOf(new BracedExpressionNode(new StringNode('Hello'))));
  }

  #[@test]
  public function registeredType() {
    with ($v= new VariableNode('h'), $t= new TypeName('util.collections.HashTable')); {
      $this->fixture->setType($v, $t);
      $this->assertEquals($t, $this->fixture->typeOf($v));
    }
  }

  #[@test]
  public function unknownType() {
    $this->assertEquals(TypeName::$VAR, $this->fixture->typeOf(new VariableNode('v')));
  }

  #[@test]
  public function objectExtension() {
    with (
      $objectType= new TypeReflection(XPClass::forName('lang.Object')), 
      $classNameMethod= new Method('getClassName')
    ); {
      $this->fixture->addExtension($objectType, $classNameMethod);
      $this->assertEquals(
        $classNameMethod,
        $this->fixture->getExtension($objectType, $classNameMethod->name)
      );
    }
  }

  #[@test]
  public function arrayExtension() {
    with (
      $objectsType= new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Object'))), 
      $sortedMethod= new Method('sorted')
    ); {
      $this->fixture->addExtension($objectsType, $sortedMethod);
      $this->assertEquals(
        $sortedMethod,
        $this->fixture->getExtension($objectsType, $sortedMethod->name)
      );
    }
  }

  #[@test]
  public function mapExtension() {
    with (
      $mapType= new MapTypeOf(new TypeReference(new TypeName('string'), Types::PRIMITIVE_KIND), new TypeReflection(XPClass::forName('lang.Object'))), 
      $keyMethod= new Method('key')
    ); {
      $this->fixture->addExtension($mapType, $keyMethod);
      $this->assertEquals(
        $keyMethod,
        $this->fixture->getExtension($mapType, $keyMethod->name)
      );
    }
  }

  #[@test]
  public function objectExtensionInherited() {
    with (
      $parentType= new TypeReflection(XPClass::forName('unittest.TestCase')),
      $thisType= new TypeReflection(typeof($this)),
      $getNameMethod= new Method('getName')
    ); {
      $this->fixture->addExtension($parentType, $getNameMethod);
      $this->assertEquals(
        $getNameMethod,
        $this->fixture->getExtension($thisType, $getNameMethod->name)
      );
    }
  }

  #[@test, @expect(ResolveException::class)]
  public function importNonExistantType() {
    $this->fixture->addTypeImport('util.cmd.@@NON_EXISTANT@@');
  }

  #[@test, @expect(ResolveException::class)]
  public function importNonExistantPackage() {
    $this->fixture->addPackageImport('util.cmd.@@NON_EXISTANT@@');
  }

  #[@test]
  public function resolveFullyQualified() {
    $this->assertEquals(
      new TypeReflection(XPClass::forName('util.cmd.Command')), 
      $this->fixture->resolveType(new TypeName('util.cmd.Command'))
    );
  }

  #[@test]
  public function resolveUnqualified() {
    $this->fixture->addTypeImport('util.cmd.Command');
    $this->assertEquals(
      new TypeReflection(XPClass::forName('util.cmd.Command')), 
      $this->fixture->resolveType(new TypeName('Command'))
    );
  }

  #[@test]
  public function resolveUnqualifiedByPackageImport() {
    $this->fixture->addPackageImport('util.cmd');
    $this->assertEquals(
      new TypeReflection(XPClass::forName('util.cmd.Command')), 
      $this->fixture->resolveType(new TypeName('Command'))
    );
  }

  #[@test]
  public function resolveArrayType() {
    $this->assertEquals(
      new TypeReference(new TypeName('util.cmd.Command[]'), Types::CLASS_KIND), 
      $this->fixture->resolveType(new TypeName('util.cmd.Command[]'))
    );
  }

  #[@test]
  public function resolveUnqualifiedArrayType() {
    $this->fixture->addPackageImport('util.cmd');
    $this->assertEquals(
      new TypeReference(new TypeName('util.cmd.Command[]'), Types::CLASS_KIND), 
      $this->fixture->resolveType(new TypeName('Command[]'))
    );
  }

  #[@test]
  public function resolveStringType() {
    $this->assertEquals(
      new TypeReference(new TypeName('string'), Types::PRIMITIVE_KIND), 
      $this->fixture->resolveType(new TypeName('string'))
    );
  }

  #[@test]
  public function resolveStringArrayType() {
    $this->assertEquals(
      new TypeReference(new TypeName('string[]'), Types::PRIMITIVE_KIND), 
      $this->fixture->resolveType(new TypeName('string[]'))
    );
  }

  #[@test]
  public function resolveGenericType() {
    $components= [new TypeName('string'), new TypeName('lang.Object')];
    $this->assertEquals(
      new GenericType(new TypeReflection(XPClass::forName('util.collections.HashTable')), $components),
      $this->fixture->resolveType(new TypeName('util.collections.HashTable', $components))
    );
  }

  #[@test]
  public function usedAfterPackageImport() {
    $this->fixture->addPackageImport('util.cmd');
    
    $this->assertEquals([], $this->fixture->used);
  }

  #[@test]
  public function usedAfterPackageAndTypeImport() {
    $this->fixture->addPackageImport('util.cmd');
    $this->fixture->resolveType(new TypeName('Command'));
    
    $this->assertEquals(['util.cmd.Command' => true], $this->fixture->used);
  }

  #[@test]
  public function usedAfterPackageAndMultipleTypeImport() {
    $this->fixture->addPackageImport('util.cmd');
    $this->fixture->resolveType(new TypeName('Command'));
    $this->fixture->resolveType(new TypeName('Command'));
    
    $this->assertEquals(['util.cmd.Command' => true], $this->fixture->used);
  }

  #[@test]
  public function usedAfterTypeImport() {
    $this->fixture->addTypeImport('util.cmd.Command');
    
    $this->assertEquals(['util.cmd.Command' => true], $this->fixture->used);
  }

  #[@test]
  public function usedAfterMultipleTypeImport() {
    $this->fixture->addTypeImport('util.cmd.Command');
    $this->fixture->addTypeImport('util.cmd.Command');
    
    $this->assertEquals(['util.cmd.Command' => true], $this->fixture->used);
  }
}