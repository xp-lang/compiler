<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\TypeDeclaration;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use xp\compiler\types\Parameter;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\InterfaceNode;
use xp\compiler\ast\EnumNode;
use xp\compiler\ast\EnumMemberNode;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\ClassConstantNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\ConstructorNode;
use xp\compiler\ast\PropertyNode;
use xp\compiler\ast\OperatorNode;
use xp\compiler\ast\IndexerNode;
use xp\compiler\ast\IntegerNode;

class TypeDeclarationTest extends \unittest\TestCase {

  #[@test]
  public function nameWithoutPackage() {
    $decl= new TypeDeclaration(new ParseTree(null, array(), new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('TestCase')
    )));
    $this->assertEquals('TestCase', $decl->name());
  }

  #[@test]
  public function nameWithPackage() {
    $decl= new TypeDeclaration(new ParseTree(new TypeName('unittest.web'), array(), new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('WebTestCase')
    )));
    $this->assertEquals('unittest.web.WebTestCase', $decl->name());
  }

  #[@test]
  public function literalWithoutPackage() {
    $decl= new TypeDeclaration(new ParseTree(null, array(), new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('TestCase')
    )));
    $this->assertEquals('TestCase', $decl->literal());
  }

  #[@test]
  public function literalWithPackage() {
    $decl= new TypeDeclaration(new ParseTree(new TypeName('unittest.web'), array(), new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('WebTestCase')
    )));
    $this->assertEquals('WebTestCase', $decl->literal());
  }

  #[@test]
  public function classKind() {
    $decl= new TypeDeclaration(new ParseTree(null, array(), new ClassNode(
      MODIFIER_PUBLIC, 
      null,
      new TypeName('TestCase')
    )));
    $this->assertEquals(Types::CLASS_KIND, $decl->kind());
  }

  #[@test]
  public function interfaceKind() {
    $decl= new TypeDeclaration(new ParseTree(null, array(), new InterfaceNode(array(
      'name' => new TypeName('Resolveable')
    ))));
    $this->assertEquals(Types::INTERFACE_KIND, $decl->kind());
  }

  #[@test]
  public function enumKind() {
    $decl= new TypeDeclaration(new ParseTree(null, array(), new EnumNode(array(
      'name' => new TypeName('Operation')
    ))));
    $this->assertEquals(Types::ENUM_KIND, $decl->kind());
  }
  
  /**
   * Returns a type declaration for the string class
   *
   * @return  xp.compiler.emit.TypeDeclaration
   */
  protected function stringClass() {
    return new TypeDeclaration(
      new ParseTree(new TypeName('lang.types'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        null,
        new TypeName('String'),
        new TypeName('lang.Object'),
        null,
        array(
          new ClassConstantNode('ENCODING', new TypeName('string'), new StringNode('utf-8')),
          new ConstructorNode(array(
          )),
          new MethodNode(array(
            'name'        => 'substring',
            'returns'     => new TypeName('lang.types.String'),
            'modifiers'   => MODIFIER_PUBLIC,
            'parameters'  => array(
              array(
                'name'    => 'start',
                'type'    => new TypeName('int'),
                'check'   => true
              ), 
              array(
                'name'    => 'end',
                'type'    => new TypeName('int'),
                'check'   => true,
                'default' => new IntegerNode(-1)
              )
            )
          )),
          new FieldNode(array(
            'name' => 'length'
          )),
          new PropertyNode(array(
            'name' => 'chars'
          )),
          new OperatorNode(array(
            'symbol' => '~'
          )),
          new IndexerNode(array(
            'type'       => new TypeName('string'),
            'parameter'  => array(
              'name'  => 'offset',
              'type'  => new TypeName('int'),
              'check' => true
            )
          ))
        )
      )),
      $this->objectClass()
    );
  }

  /**
   * Returns a type declaration for the coin enum
   *
   * @return  xp.compiler.emit.TypeDeclaration
   */
  protected function coinEnum() {
    return new TypeDeclaration(
      new ParseTree(new TypeName('util.money'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        null,
        new TypeName('Coin'),
        new TypeName('lang.Enum'),
        null,
        array(
          new EnumMemberNode(array('name' => 'penny', 'value' => new IntegerNode('1'), 'body' => null)),
          new EnumMemberNode(array('name' => 'nickel', 'value' => new IntegerNode('2'), 'body' => null)),
          new EnumMemberNode(array('name' => 'dime', 'value' => new IntegerNode('10'), 'body' => null)),
          new EnumMemberNode(array('name' => 'quarter', 'value' => new IntegerNode('25'), 'body' => null)),
        )
      )),
      $this->objectClass()
    );
  }

  /**
   * Returns a type declaration for the object class
   *
   * @return  xp.compiler.emit.TypeDeclaration
   */
  protected function objectClass() {
    return new TypeDeclaration(
      new ParseTree(new TypeName('lang'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        null,
        new TypeName('Object'),
        null,
        null,
        array(
          new MethodNode(array(
            'name' => 'equals'
          ))
        )
      ))
    );
  }

  /**
   * Returns a type declaration for the SecureString class
   *
   * @return  xp.compiler.emit.TypeDeclaration
   */
  protected function secureStringClass() {
    return new TypeDeclaration(
      new ParseTree(new TypeName('security'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        null,
        new TypeName('SecureString'),
        new TypeName('lang.types.String'),
        null,
        array(
        )
      )),
      $this->stringClass()
    );
  }

  #[@test]
  public function objectClassHasNoConstructor() {
    $decl= $this->objectClass();
    $this->assertFalse($decl->hasConstructor());
  }

  #[@test]
  public function objectClassNoConstructor() {
    $decl= $this->objectClass();
    $this->assertNull($decl->getConstructor());
  }

  #[@test]
  public function stringClassHasConstructor() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasConstructor());
  }

  #[@test]
  public function secureStringClassHasConstructor() {
    $decl= $this->secureStringClass();
    $this->assertTrue($decl->hasConstructor());
  }

  #[@test]
  public function stringClassConstructor() {
    $decl= $this->stringClass();
    $this->assertInstanceOf('xp.compiler.types.Constructor', $decl->getConstructor());
  }

  #[@test]
  public function secureStringClassConstructor() {
    $decl= $this->secureStringClass();
    $this->assertEquals($this->stringClass(), $decl->getConstructor()->holder);
  }

  #[@test]
  public function secureStringClassConstructorsHolderIsStringClass() {
    $decl= $this->secureStringClass();
    $this->assertEquals('lang.types.String', $decl->getConstructor()->holder->name());
  }

  #[@test]
  public function objectClassHasMethod() {
    $decl= $this->objectClass();
    $this->assertTrue($decl->hasMethod('equals'), 'equals');
    $this->assertFalse($decl->hasMethod('getName'), 'getName');
  }

  #[@test]
  public function stringClassHasEqualsMethod() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasMethod('equals'));
  }

  #[@test]
  public function stringClassHasSubstringMethod() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasMethod('substring'));
  }

  #[@test]
  public function stringClassDoesNotHaveGetNameMethod() {
    $decl= $this->stringClass();
    $this->assertFalse($decl->hasMethod('getName'));
  }

  #[@test]
  public function stringClassSubstringMethod() {
    $method= $this->stringClass()->getMethod('substring');
    $this->assertEquals(new TypeName('lang.types.String'), $method->returns);
    $this->assertEquals('substring', $method->name);
    $this->assertEquals(
      array(
        new Parameter('start', new TypeName('int')),
        new Parameter('end', new TypeName('int'), new IntegerNode(-1))
      ),
      $method->parameters
    );
    $this->assertEquals(MODIFIER_PUBLIC, $method->modifiers);
  }

  #[@test]
  public function objectClassDoesNotHaveOperator() {
    $decl= $this->objectClass();
    $this->assertFalse($decl->hasOperator('~'));
  }

  #[@test]
  public function objectClassNoOperator() {
    $decl= $this->objectClass();
    $this->assertNull($decl->getOperator('~'));
  }

  #[@test]
  public function stringClassHasConcatOperator() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasOperator('~'));
  }

  #[@test]
  public function stringClassConcatOperator() {
    $decl= $this->stringClass();
    $this->assertInstanceOf('xp.compiler.types.Operator', $decl->getOperator('~'));
  }

  #[@test]
  public function secureStringClassHasConcatOperator() {
    $decl= $this->secureStringClass();
    $this->assertTrue($decl->hasOperator('~'));
  }

  #[@test]
  public function secureStringClassConcatOperator() {
    $decl= $this->secureStringClass();
    $this->assertInstanceOf('xp.compiler.types.Operator', $decl->getOperator('~'));
  }

  #[@test]
  public function secureStringClassConcatOperatorsHolderIsString() {
    $decl= $this->secureStringClass();
    $this->assertEquals('lang.types.String', $decl->getOperator('~')->holder->name());
  }

  #[@test]
  public function objectClassDoesNotHaveProperty() {
    $decl= $this->objectClass();
    $this->assertFalse($decl->hasProperty('chars'));
  }

  #[@test]
  public function objectClassNoProperty() {
    $decl= $this->objectClass();
    $this->assertNull($decl->getProperty('chars'));
  }

  #[@test]
  public function stringClassHasConcatProperty() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasProperty('chars'));
  }

  #[@test]
  public function stringClassConcatProperty() {
    $decl= $this->stringClass();
    $this->assertInstanceOf('xp.compiler.types.Property', $decl->getProperty('chars'));
  }

  #[@test]
  public function secureStringClassHasConcatProperty() {
    $decl= $this->secureStringClass();
    $this->assertTrue($decl->hasProperty('chars'));
  }

  #[@test]
  public function secureStringClassConcatProperty() {
    $decl= $this->secureStringClass();
    $this->assertInstanceOf('xp.compiler.types.Property', $decl->getProperty('chars'));
  }

  #[@test]
  public function secureStringClassConcatPropertysHolderIsString() {
    $decl= $this->secureStringClass();
    $this->assertEquals('lang.types.String', $decl->getProperty('chars')->holder->name());
  }

  #[@test]
  public function stringClassHasLengthField() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasField('length'));
  }

  #[@test]
  public function stringClassDoesNotHaveCharsField() {
    $decl= $this->stringClass();
    $this->assertFalse($decl->hasField('chars'));
  }

  #[@test]
  public function stringClassHasIndexer() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasIndexer());
  }

  #[@test]
  public function secureStringClassHasIndexer() {
    $decl= $this->secureStringClass();
    $this->assertTrue($decl->hasIndexer());
  }

  #[@test]
  public function objectClassDoesNotHaveIndexer() {
    $decl= $this->objectClass();
    $this->assertFalse($decl->hasIndexer());
  }

  #[@test]
  public function stringClassIndexer() {
    $indexer= $this->stringClass()->getIndexer();
    $this->assertEquals(new TypeName('string'), $indexer->type);
    $this->assertEquals(new TypeName('int'), $indexer->parameter);
  }

  #[@test]
  public function secureStringClassIndexer() {
    $indexer= $this->secureStringClass()->getIndexer();
    $this->assertEquals(new TypeName('string'), $indexer->type);
    $this->assertEquals(new TypeName('int'), $indexer->parameter);
  }

  #[@test]
  public function objectClassIsNotEnumerable() {
    $decl= $this->objectClass();
    $this->assertFalse($decl->isEnumerable());
  }

  #[@test]
  public function objectClassDoesNotHaveConstant() {
    $decl= $this->objectClass();
    $this->assertFalse($decl->hasConstant('STATUS_OK'));
  }

  #[@test]
  public function stringClassHasConstant() {
    $decl= $this->stringClass();
    $this->assertTrue($decl->hasConstant('ENCODING'));
  }

  #[@test]
  public function stringClassGetConstant() {
    $this->assertNull($this->objectClass()->getConstant('STATUS_OK'));
  }

  #[@test]
  public function stringClassConstant() {
    $const= $this->stringClass()->getConstant('ENCODING');
    $this->assertEquals(new TypeName('string'), $const->type);
    $this->assertEquals('utf-8', $const->value);
  }

  #[@test]
  public function stringClassSubclassOfObject() {
    $this->assertTrue($this->stringClass()->isSubclassOf($this->objectClass()));
  }

  #[@test]
  public function extendedStringClassSubclassOfObject() {
    $decl= new TypeDeclaration(
      new ParseTree(new TypeName('lang.types'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        null,
        new TypeName('ExtendedString'),
        new TypeName('lang.types.String'),
        null,
        array()
      )),
      $this->stringClass()
    );
    $this->assertTrue($decl->isSubclassOf($this->objectClass()));
  }

  #[@test]
  public function coinEnumHasMemberField() {
    $this->assertTrue($this->coinEnum()->hasField('penny'));
  }

  #[@test]
  public function getExtensionsFromStringClass() {
    $this->assertEquals(array(), $this->stringClass()->getExtensions());
  }

  #[@test]
  public function getExtensionsFromArrayListExtensionsClass() {
    $decl= new TypeDeclaration(
      new ParseTree(new TypeName('lang.types'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        null,
        new TypeName('ArraySortingExtensions'),
        new TypeName('lang.Object'),
        null,
        array(
          new MethodNode(array(
            'name'        => 'sorted',
            'returns'     => new TypeName('lang.types.ArrayList'),
            'extension'   => new TypeName('lang.types.ArrayList'),
            'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
            'parameters'  => array(
              array(
                'name'  => 'self',
                'type'  => new TypeName('lang.types.ArrayList'),
                'check' => true
              ), 
            )
          )),
        )
      )),
      $this->objectClass()
    );
    $extensions= $decl->getExtensions();

    $this->assertEquals(1, sizeof($extensions));
    $this->assertEquals('lang.types.ArrayList', key($extensions));
    $this->assertEquals('sorted', $extensions['lang.types.ArrayList'][0]->name());
  }
}