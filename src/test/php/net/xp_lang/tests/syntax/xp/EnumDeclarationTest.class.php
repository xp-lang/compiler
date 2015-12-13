<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\ast\EnumNode;
use xp\compiler\ast\EnumMemberNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class EnumDeclarationTest extends ParserTestCase {

  /**
   * Parse enum source and return body.
   *
   * @param   string src
   * @return  xp.compiler.Node
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration;
  }

  /**
   * Test enum declaration
   *
   */
  #[@test]
  public function emtpyEnum() {
    $this->assertEquals(new EnumNode(
      0,
      null,
      new TypeName('Days'),
      null,
      [],
      null
    ), $this->parse('enum Days { }'));
  }

  /**
   * Test enum declaration
   *
   */
  #[@test]
  public function abstractEnum() {
    $this->assertEquals(new EnumNode(
      MODIFIER_ABSTRACT,
      null,
      new TypeName('Days'),
      null,
      [],
      null
    ), $this->parse('abstract enum Days { }'));
  }

  /**
   * Test enum declaration
   *
   */
  #[@test]
  public function daysEnum() {
    $this->assertEquals([
      new EnumMemberNode(['name' => 'monday', 'body' => null]),
      new EnumMemberNode(['name' => 'tuesday', 'body' => null]),
      new EnumMemberNode(['name' => 'wednedsday', 'body' => null]),
      new EnumMemberNode(['name' => 'thursday', 'body' => null]),
      new EnumMemberNode(['name' => 'friday', 'body' => null]),
      new EnumMemberNode(['name' => 'saturday', 'body' => null]),
      new EnumMemberNode(['name' => 'sunday', 'body' => null]),
    ], $this->parse('enum Days { monday, tuesday, wednedsday, thursday, friday, saturday, sunday }')->body);
  }

  /**
   * Test enum declaration
   *
   */
  #[@test]
  public function coinEnum() {
    $this->assertEquals(new EnumNode(0, null, new TypeName('Coin'), null, [], [
      new EnumMemberNode([
        'name'      => 'penny', 
        'value'     => new IntegerNode('1'),
        'body'      => null
      ]),
      new EnumMemberNode([
        'name'      => 'nickel', 
        'value'     => new IntegerNode('2'),
        'body'      => null
      ]),
      new EnumMemberNode([
        'name'      => 'dime', 
        'value'     => new IntegerNode('10'),
        'body'      => null
      ]),
      new EnumMemberNode([
        'name'      => 'quarter', 
        'value'     => new IntegerNode('25'),
        'body'      => null
      ]),
      new MethodNode([
        'modifiers'    => MODIFIER_PUBLIC,
        'annotations'  => null,
        'returns'      => new TypeName('string'),
        'name'         => 'color',
        'parameters'   => null,
        'throws'       => null,
        'body'         => [],
        'extension'    => null
      ])
    ]), $this->parse('enum Coin { 
      penny(1), nickel(2), dime(10), quarter(25);
      
      public string color() {
        // TBI
      }
    }'));
  }
}
