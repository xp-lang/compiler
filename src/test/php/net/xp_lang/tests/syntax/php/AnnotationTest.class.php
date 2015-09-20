<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\types\TypeName;
use xp\compiler\ast\AnnotationNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\HexNode;
use xp\compiler\ast\DecimalNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\MapNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\ConstantAccessNode;
use xp\compiler\ast\ClassNameAccessNode;
use xp\compiler\ast\StaticMemberAccessNode;

class AnnotationTest extends ParserTestCase {

  /**
   * Parse method annotations and return annotations
   *
   * @param   string annotations
   * @return  xp.compiler.Node[]
   */
  protected function parseMethodWithAnnotations($annotations) {
    return (new Parser())->parse(new Lexer('<?php abstract class Container {
      '.$annotations.'
      public abstract function method();
    } ?>', '<string:'.$this->name.'>'))->declaration->body[0]->annotations;
  }

  #[@test]
  public function noAnnotation() {
    $this->assertNull($this->parseMethodWithAnnotations(''));
  }

  #[@test]
  public function simpleAnnotation() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Test'
    ))), $this->parseMethodWithAnnotations('#[@Test]'));
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function simpleAnnotationWithBrackets() {
    $this->parseMethodWithAnnotations('#[@Test()]');
  }

  #[@test]
  public function annotationWithStringValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Expect',
      'parameters'    => array('default' => new StringNode('lang.IllegalArgumentException'))
    ))), $this->parseMethodWithAnnotations('#[@Expect("lang.IllegalArgumentException")]'));
  }

  #[@test]
  public function annotationWithClasssName() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Expect',
      'parameters'    => array('default' => new ClassNameAccessNode(new TypeName('IllegalArgumentException')))
    ))), $this->parseMethodWithAnnotations('#[@Expect(IllegalArgumentException::class)]'));
  }

  #[@test]
  public function annotationWithIntegerValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Limit',
      'parameters'    => array('default' => new IntegerNode('5'))
    ))), $this->parseMethodWithAnnotations('#[@Limit(5)]'));
  }

  #[@test]
  public function annotationWithHexValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Limit',
      'parameters'    => array('default' => new HexNode('0x5'))
    ))), $this->parseMethodWithAnnotations('#[@Limit(0x5)]'));
  }

  #[@test]
  public function annotationWithDecimalValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Limit',
      'parameters'    => array('default' => new DecimalNode('5.0'))
    ))), $this->parseMethodWithAnnotations('#[@Limit(5.0)]'));
  }

  #[@test]
  public function annotationWithnullValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Limit',
      'parameters'    => array('default' => new NullNode())
    ))), $this->parseMethodWithAnnotations('#[@Limit(null)]'));
  }

  #[@test]
  public function annotationWithtrueValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Limit',
      'parameters'    => array('default' => new BooleanNode(true))
    ))), $this->parseMethodWithAnnotations('#[@Limit(true)]'));
  }

  #[@test]
  public function annotationWithfalseValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Limit',
      'parameters'    => array('default' => new BooleanNode(false))
    ))), $this->parseMethodWithAnnotations('#[@Limit(false)]'));
  }

  #[@test]
  public function annotationWithArrayValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Restrict',
      'parameters'    => array('default' => new ArrayNode(array(
        'values'        => array(
          new StringNode('Admin'),
          new StringNode('Root'),
        ),
        'type'          => null
      )))
    ))), $this->parseMethodWithAnnotations('#[@Restrict(array("Admin", "Root"))]'));
  }

  #[@test]
  public function annotationWithShortArrayValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Restrict',
      'parameters'    => array('default' => new ArrayNode(array(
        'values'        => array(
          new StringNode('Admin'),
          new StringNode('Root'),
        ),
        'type'          => null
      )))
    ))), $this->parseMethodWithAnnotations('#[@Restrict(["Admin", "Root"])]'));
  }

  #[@test]
  public function annotationWithMapValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Restrict',
      'parameters'    => array('default' => new MapNode(array(
        'elements'      => array(array(
          new StringNode('Role'),
          new StringNode('Root'),
        )),
        'type'          => null
      )))
    ))), $this->parseMethodWithAnnotations('#[@Restrict(array("Role" => "Root"))]'));
  }

  #[@test]
  public function annotationWithShortMapValue() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Restrict',
      'parameters'    => array('default' => new MapNode(array(
        'elements'      => array(array(
          new StringNode('Role'),
          new StringNode('Root'),
        )),
        'type'          => null
      )))
    ))), $this->parseMethodWithAnnotations('#[@Restrict(["Role" => "Root"])]'));
  }

  #[@test]
  public function annotationWithValues() {
    $this->assertEquals(array(new AnnotationNode(array(
      'type'          => 'Expect',
      'parameters'    => array(
        'classes' => new ArrayNode(array(
          'values'        => array(
            new StringNode('lang.IllegalArgumentException'),
            new StringNode('lang.IllegalAccessException'),
          ),
          'type'          => null
        )),
        'code'    => new IntegerNode('503'),
      )))
    ), $this->parseMethodWithAnnotations('#[@Expect(
      classes = array("lang.IllegalArgumentException", "lang.IllegalAccessException"),
      code    = 503
    )]'));
  }

  #[@test]
  public function multipleAnnotations() {
    $this->assertEquals(array(
      new AnnotationNode(array('type' => 'WebMethod')),
      new AnnotationNode(array('type' => 'Deprecated')),
    ), $this->parseMethodWithAnnotations('#[@WebMethod, @Deprecated]'));
  }

  #[@test]
  public function newinstance() {
    $this->assertEquals(
      array(new AnnotationNode(array(
        'type'       => 'action',
        'parameters' => array('default' => new InstanceCreationNode(array(
          'type'       => new TypeName('IsPlatform'),
          'parameters' => array(new StringNode('WIN'))
        )))
      ))),
      $this->parseMethodWithAnnotations('#[@action(new IsPlatform("WIN"))]')
    );
  }

  #[@test]
  public function newinstance_fully_qualified() {
    $this->assertEquals(
      array(new AnnotationNode(array(
        'type'       => 'action',
        'parameters' => array('default' => new InstanceCreationNode(array(
          'type'       => new TypeName('unittest.actions.IsPlatform'),
          'parameters' => array(new StringNode('WIN'))
        )))
      ))),
      $this->parseMethodWithAnnotations('#[@action(new \unittest\actions\IsPlatform("WIN"))]')
    );
  }

  #[@test]
  public function constant_reference() {
    $this->assertEquals(
      array(new AnnotationNode(array(
        'type'       => 'inject',
        'parameters' => array('name' => new ConstantAccessNode(new TypeName('self'), 'CONNECTION_DSN'))
      ))),
      $this->parseMethodWithAnnotations('#[@inject(name = self::CONNECTION_DSN)]')
    );
  }

  #[@test]
  public function static_member() {
    $this->assertEquals(
      array(new AnnotationNode(array(
        'type'       => 'value',
        'parameters' => array('default' => new StaticMemberAccessNode(new TypeName('CommandLine'), 'UNIX'))
      ))),
      $this->parseMethodWithAnnotations('#[@value(CommandLine::$UNIX)]')
    );
  }
}
