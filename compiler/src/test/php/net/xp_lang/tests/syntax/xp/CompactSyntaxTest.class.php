<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   * @see   https://github.com/xp-framework/rfc/issues/240
   * @see   https://github.com/xp-framework/rfc/issues/241
   * @see   https://github.com/xp-framework/rfc/issues/252
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

    /**
     * Test compact assignment
     *
     */
    #[@test]
    public function compact_assignment() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'setName',
        'returns'    => TypeName::$VOID,
        'parameters' => array(
          array(
            'assign' => 'name',
          )
        ),
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public void setName($this.name) { }
      }'));
    }

    /**
     * Test compact assignment
     *
     */
    #[@test]
    public function compact_assignment_with_default() {
      $this->assertEquals(array(new ConstructorNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'parameters' => array(
          array(
            'assign'  => 'name',
            'default' => new NullNode()
          )
        ),
        'throws'     => NULL,
        'body'       => array()
      ))), $this->parse('class Null { 
        public __construct($this.name= null) { }
      }'));
    }

    /**
     * Test compact fluent interface
     *
     */
    #[@test]
    public function compact_fluent_return_this() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'withName',
        'returns'    => new TypeName('self'),
        'parameters' => array(
          array(
            'assign' => 'name',
          )
        ),
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public this withName($this.name) { }
      }'));
    }
  }
?>
