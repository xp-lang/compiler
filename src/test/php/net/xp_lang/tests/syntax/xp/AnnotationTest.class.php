<?php namespace net\xp_lang\tests\syntax\xp;

use text\parser\generic\ParseException;
use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\types\TypeName;
use xp\compiler\ast\AnnotationNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\DecimalNode;
use xp\compiler\ast\HexNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\MapNode;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\ConstantAccessNode;
use xp\compiler\ast\StaticMemberAccessNode;

/**
 * TestCase
 *
 */
class AnnotationTest extends ParserTestCase {

  /**
   * Parse method annotations and return annotations
   *
   * @param   string annotations
   * @return  xp.compiler.Node[]
   */
  protected function parseMethodWithAnnotations($annotations) {
    return (new Parser())->parse(new Lexer('abstract class Container {
      '.$annotations.'
      public abstract void method();
    }', '<string:'.$this->name.'>'))->declaration->body[0]->annotations;
  }

  /**
   * Test no annotation
   *
   */
  #[@test]
  public function noAnnotation() {
    $this->assertEquals(null, $this->parseMethodWithAnnotations(''));
  }

  /**
   * Test simple annotation (Test)
   *
   */
  #[@test]
  public function simpleAnnotation() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Test'
    ])], $this->parseMethodWithAnnotations('[@Test]'));
  }

  /**
   * Test simple annotation (Test)
   *
   */
  #[@test, @expect(ParseException::class)]
  public function simpleAnnotationWithBrackets() {
    $this->parseMethodWithAnnotations('[@Test()]');
  }

  /**
   * Test annotation with default value (Expect("lang.IllegalArgumentException"))
   *
   */
  #[@test]
  public function annotationWithStringValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Expect',
      'parameters'    => ['default' => new StringNode('lang.IllegalArgumentException')]
    ])], $this->parseMethodWithAnnotations('[@Expect("lang.IllegalArgumentException")]'));
  }

  /**
   * Test annotation with default value (Limit(5)))
   *
   */
  #[@test]
  public function annotationWithIntegerValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Limit',
      'parameters'    => ['default' => new IntegerNode('5')]
    ])], $this->parseMethodWithAnnotations('[@Limit(5)]'));
  }

  /**
   * Test annotation with default value (Limit(0x5)))
   *
   */
  #[@test]
  public function annotationWithHexValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Limit',
      'parameters'    => ['default' => new HexNode('0x5')]
    ])], $this->parseMethodWithAnnotations('[@Limit(0x5)]'));
  }

  /**
   * Test annotation with default value (Limit(5.0)))
   *
   */
  #[@test]
  public function annotationWithDecimalValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Limit',
      'parameters'    => ['default' => new DecimalNode('5.0')]
    ])], $this->parseMethodWithAnnotations('[@Limit(5.0)]'));
  }

  /**
   * Test annotation with default value (Limit(null)))
   *
   */
  #[@test]
  public function annotationWithNullValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Limit',
      'parameters'    => ['default' => new NullNode()]
    ])], $this->parseMethodWithAnnotations('[@Limit(null)]'));
  }

  /**
   * Test annotation with default value (Limit(true)))
   *
   */
  #[@test]
  public function annotationWithTrueValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Limit',
      'parameters'    => ['default' => new BooleanNode(true)]
    ])], $this->parseMethodWithAnnotations('[@Limit(true)]'));
  }

  /**
   * Test annotation with default value (Limit(false)))
   *
   */
  #[@test]
  public function annotationWithFalseValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Limit',
      'parameters'    => ['default' => new BooleanNode(false)]
    ])], $this->parseMethodWithAnnotations('[@Limit(false)]'));
  }

  /**
   * Test annotation with default value (Restrict(["Admin", "Root"]))
   *
   */
  #[@test]
  public function annotationWithArrayValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Restrict',
      'parameters'    => ['default' => new ArrayNode([
        'values'        => [
          new StringNode('Admin'),
          new StringNode('Root'),
        ],
        'type'          => null
      ])]
    ])], $this->parseMethodWithAnnotations('[@Restrict(["Admin", "Root"])]'));
  }

  /**
   * Test annotation with default value (Restrict([Role : "Root"]))
   *
   */
  #[@test]
  public function annotationWithMapValue() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Restrict',
      'parameters'    => ['default' => new MapNode([
        'elements'      => [[
          new StringNode('Role'),
          new StringNode('Root'),
        ]],
        'type'          => null
      ])]
    ])], $this->parseMethodWithAnnotations('[@Restrict([Role : "Root"])]'));
  }

  /**
   * Test annotation with key/value pairs (Expect(classes = [...], code = 503))
   *
   */
  #[@test]
  public function annotationWithValues() {
    $this->assertEquals([new AnnotationNode([
      'type'          => 'Expect',
      'parameters'    => [
        'classes' => new ArrayNode([
          'values'        => [
            new StringNode('lang.IllegalArgumentException'),
            new StringNode('lang.IllegalAccessException'),
          ],
          'type'          => null
        ]),
        'code'    => new IntegerNode('503'),
      ]])
    ], $this->parseMethodWithAnnotations('[@Expect(
      classes = ["lang.IllegalArgumentException", "lang.IllegalAccessException"],
      code    = 503
    )]'));
  }

  /**
   * Test multiple annotations (WebMethod, Deprecated)
   *
   */
  #[@test]
  public function multipleAnnotations() {
    $this->assertEquals([
      new AnnotationNode(['type' => 'WebMethod']),
      new AnnotationNode(['type' => 'Deprecated']),
    ], $this->parseMethodWithAnnotations('[@WebMethod, @Deprecated]'));
  }

  /**
   * Test target annotations
   *
   */
  #[@test]
  public function targetAnnotations() {
    $this->assertEquals(
      [new AnnotationNode(['type' => 'Inject', 'target' => '$conn'])],
      $this->parseMethodWithAnnotations('[@$conn: Inject]')
    );
  }

  #[@test]
  public function newinstance() {
    $this->assertEquals(
      [new AnnotationNode([
        'type'       => 'action',
        'parameters' => ['default' => new InstanceCreationNode([
          'type'       => new TypeName('IsPlatform'),
          'parameters' => [new StringNode('WIN')]
        ])]
      ])],
      $this->parseMethodWithAnnotations('[@action(new IsPlatform("WIN"))]')
    );
  }

  #[@test]
  public function newinstance_fully_qualified() {
    $this->assertEquals(
      [new AnnotationNode([
        'type'       => 'action',
        'parameters' => ['default' => new InstanceCreationNode([
          'type'       => new TypeName('unittest.actions.IsPlatform'),
          'parameters' => [new StringNode('WIN')]
        ])]
      ])],
      $this->parseMethodWithAnnotations('[@action(new unittest.actions.IsPlatform("WIN"))]')
    );
  }

  #[@test]
  public function constant_reference() {
    $this->assertEquals(
      [new AnnotationNode([
        'type'       => 'inject',
        'parameters' => ['name' => new ConstantAccessNode(new TypeName('self'), 'CONNECTION_DSN')]
      ])],
      $this->parseMethodWithAnnotations('[@inject(name = self::CONNECTION_DSN)]')
    );
  }

  #[@test]
  public function static_member() {
    $this->assertEquals(
      [new AnnotationNode([
        'type'       => 'value',
        'parameters' => ['default' => new StaticMemberAccessNode(new TypeName('CommandLine'), 'UNIX')]
      ])],
      $this->parseMethodWithAnnotations('[@value(CommandLine::$UNIX)]')
    );
  }
}
