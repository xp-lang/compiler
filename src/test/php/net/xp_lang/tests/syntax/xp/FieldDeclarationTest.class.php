<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\PropertyNode;
use xp\compiler\ast\IndexerNode;
use xp\compiler\ast\NullNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class FieldDeclarationTest extends ParserTestCase {

  /**
   * Parse class source and return statements inside field declaration
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function publicField() {
    $this->assertEquals(array(new FieldNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'name',
      'type'       => new TypeName('string'),
      'initialization' => null,
    ))), $this->parse('class Person { 
      public string $name;
    }'));
  }

  /**
   * Test field declaration
   *
   */
  #[@test]
  public function privateStaticField() {
    $this->assertEquals(array(new FieldNode(array(
      'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
      'annotations'     => null,
      'name'            => 'instance',
      'type'            => new TypeName('self'),
      'initialization'  => new NullNode()
    ))), $this->parse('class Logger { 
      private static self $instance= null;
    }'));
  }

  /**
   * Test property declaration
   *
   */
  #[@test]
  public function readOnlyProperty() {
    $this->assertEquals(array(
      new FieldNode(array(
        'modifiers'  => MODIFIER_PRIVATE,
        'annotations'=> null,
        'name'       => '_name',
        'type'       => new TypeName('string'),
        'initialization' => null,
      )),
      new PropertyNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> null,
        'type'       => new TypeName('string'),
        'name'       => 'name',
        'handlers'   => array(
          'get' => array(
            new ReturnNode(new MemberAccessNode(new VariableNode('this'), '_name'))
          ),
        )
      ))
    ), $this->parse('class Person {
      private string $_name;
      public string name { get { return $this._name; } }
    }'));
  }

  /**
   * Test property declaration
   *
   */
  #[@test]
  public function readWriteProperty() {
    $this->assertEquals(array(
      new FieldNode(array(
        'modifiers'  => MODIFIER_PRIVATE,
        'annotations'=> null,
        'name'       => '_name',
        'type'       => new TypeName('string'),
        'initialization' => null,
      )),
      new PropertyNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> null,
        'type'       => new TypeName('string'),
        'name'       => 'name',
        'handlers'   => array(
          'get' => array(
            new ReturnNode(new MemberAccessNode(new VariableNode('this'), '_name'))
          ),
          'set' => array(
            new AssignmentNode(array(
              'variable'   => new MemberAccessNode(new VariableNode('this'), '_name'),
              'expression' => new VariableNode('value'),
              'op'         => '='
            ))
          )
        )
      ))
    ), $this->parse('class Person {
      private string $_name;
      public string name { 
        get { return $this._name; } 
        set { $this._name= $value; }
      }
    }'));
  }

  /**
   * Test indexer declaration
   *
   */
  #[@test]
  public function indexer() {
    $this->assertEquals(array(
      new IndexerNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> null,
        'type'       => new TypeName('T'),
        'parameter'  => array(
          'name'  => 'offset',
          'type'  => new TypeName('int'),
          'check' => true
        ),
        'handlers'   => array(
          'get'   => null,
          'set'   => null,
          'isset' => null,
          'unset' => null
        )
      ))
    ), $this->parse('class ArrayList {
      public T this[int $offset] { 
        get {  } 
        set {  }
        isset {  }
        unset {  }
      }
    }'));
  }
}
