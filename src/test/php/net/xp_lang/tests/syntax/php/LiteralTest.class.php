<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\DecimalNode;
use xp\compiler\ast\HexNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\MapNode;
use xp\compiler\ast\BracedExpressionNode;

class LiteralTest extends ParserTestCase {

  #[@test]
  public function doubleQuotedStringLiteral() {
    $this->assertEquals(array(new StringNode('Hello World')), $this->parse('"Hello World";'));
  }

  #[@test]
  public function singleQuotedStringLiteral() {
    $this->assertEquals(array(new StringNode('Hello World')), $this->parse("'Hello World';"));
  }

  #[@test]
  public function emptyStrings() {
    $this->assertEquals(array(
      new StringNode(''),
      new StringNode(''),
    ), $this->parse('""; \'\';'));
  }

  #[@test]
  public function doubleQuotedStringWithEscapes() {
    $this->assertEquals(
      array(new StringNode('"Hello", he said')),
      $this->parse('"\"Hello\", he said";')
    );
  }

  #[@test]
  public function singleQuotedStringWithEscapes() {
    $this->assertEquals(
      array(new StringNode("Timm's e-mail address")),
      $this->parse("'Timm\'s e-mail address';")
    );
  }

  #[@test]
  public function multiLineString() {
    $this->assertEquals(array(new StringNode('This
       is 
       a
       multiline
       string'
    )), $this->parse("
      'This
       is 
       a
       multiline
       string';
    "));
  }

  #[@test]
  public function numberLiteral() {
    $this->assertEquals(array(new IntegerNode('1')), $this->parse('1;'));
  }

  #[@test]
  public function negativeInt() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new IntegerNode('1'),
      'op'            => '-'
    ))), $this->parse("
      -1;
    "));
  }

  #[@test]
  public function negativeDecimal() {
    $this->assertEquals(array(new UnaryOpNode(array(
      'expression'    => new DecimalNode('1.0'),
      'op'            => '-'
    ))), $this->parse("
      -1.0;
    "));
  }

  #[@test]
  public function hexLiteral() {
    $this->assertEquals(array(new HexNode('0x0')), $this->parse('0x0;'));
  }

  #[@test]
  public function decimalLiteral() {
    $this->assertEquals(array(new DecimalNode('1.0')), $this->parse('1.0;'));
  }

  #[@test]
  public function booleantrueLiteral() {
    $this->assertEquals(
      array(new BooleanNode(true)),
      $this->parse('true;')
    );
  }

  #[@test, @ignore('Recognized as cast')]
  public function booleantrueLiteralInBraces() {
    $this->assertEquals(
      array(new BracedExpressionNode(new BooleanNode(true))),
      $this->parse('(true);')
    );
  }

  #[@test]
  public function booleanfalseLiteral() {
    $this->assertEquals(
      array(new BooleanNode(false)),
      $this->parse('false;')
    );
  }

  #[@test]
  public function nullLiteral() {
    $this->assertEquals(
      array(new NullNode()),
      $this->parse('null;')
    );
  }

  #[@test]
  public function arrayLiteral() {
    $this->assertEquals(array(new ArrayNode(array(
      'values'        => array(
        new IntegerNode('1'),
        new IntegerNode('2'),
      ),
      'type'          => null
    ))), $this->parse("
      array(1, 2);
    "));
  }

  #[@test]
  public function mapLiteral() {
    $this->assertEquals(array(new MapNode(array(
      'elements'      => array(array(
        new StringNode('one'),
        new IntegerNode('1')
      )),
      'type'          => null
    ))), $this->parse("
      array('one' => 1);
    "));
  }
}
