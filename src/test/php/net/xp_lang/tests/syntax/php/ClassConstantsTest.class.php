<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\ast\ClassConstantNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\NullNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class ClassConstantsTest extends ParserTestCase {

  /**
   * Parse class source and return statements inside field declaration
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer('<?php '.$src.' ?>', '<string:'.$this->name.'>'))->declaration->body;
  }

  #[@test]
  public function stringConstant() {
    $this->assertEquals(
      array(new ClassConstantNode('GET', new TypeName('var'), new StringNode('GET'))),
      $this->parse('class HttpMethods { const GET = "GET"; }')
    );
  }

  #[@test]
  public function intConstant() {
    $this->assertEquals(
      array(new ClassConstantNode('THRESHHOLD', new TypeName('var'), new IntegerNode('5'))),
      $this->parse('class Policy { const THRESHHOLD = 5; }')
    );
  }

  #[@test]
  public function varConstant() {
    $this->assertEquals(
      array(new ClassConstantNode('EMPTYNESS', new TypeName('var'), new NullNode())),
      $this->parse('class Example { const EMPTYNESS = null; }')
    );
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function constantsCanOnlyBePrimitives() {
    $this->parse('class Policy { const THRESHHOLD = new Object(); }');
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function noArraysAllowed() {
    $this->parse('class Numb3rs { const FIRST_THREE = array(1, 2, 3); }');
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function noMapsAllowed() {
    $this->parse('class Numb3rs { const FIRST_THREE = array(1 => "One", 2 => "Two", 3 => "Three"); }');
  }
}