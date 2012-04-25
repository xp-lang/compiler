<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   * @see   https://github.com/xp-framework/rfc/issues/241
   */
  class CompactSyntaxTest extends ParserTestCase {

    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      try {
        return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer(
          $src, 
          '<string:'.$this->name.'>'
        ))->declaration->body;
      } catch (ParseException $e) {
        throw $e->getCause();
      }
    }
  
    /**
     * Test "-> (expr)" as shorthand for "{ return (expr); }"
     *
     */
    #[@test]
    public function compact_return() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'getName',
        'returns'    => new TypeName('string'),
        'parameters' => NULL,
        'throws'     => NULL,
        'body'       => array(
          new ReturnNode(new MemberAccessNode(new VariableNode('this'), 'name'))
        ),
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public string getName() -> $this.name;
      }'));
    }
  }
?>
