<?php namespace net\xp_lang\tests\syntax\xp;

use lang\FormatException;
use xp\compiler\ast\InstanceCreationNode;
use xp\compiler\ast\InstanceOfNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\CloneNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class ObjectOperationTest extends ParserTestCase {

  /**
   * Test new
   *
   */
  #[@test]
  public function instanceCreation() {
    $this->assertEquals(
      [new InstanceCreationNode([
        'type'       => new TypeName('XPClass'),
        'parameters' => null
      ])],
      $this->parse('new XPClass();')
    );
  }

  /**
   * Test new with generics
   *
   */
  #[@test]
  public function genericInstanceCreation() {
    $this->assertEquals(
      [new InstanceCreationNode([
        'type'       => new TypeName('Filter', [new TypeName('String')]),
        'parameters' => null
      ])],
      $this->parse('new Filter<String>();')
    );
  }

  /**
   * Test new with generics
   *
   */
  #[@test]
  public function genericOfGenericInstanceCreation() {
    $this->assertEquals(
      [new InstanceCreationNode([
        'type'       => new TypeName('Filter', [new TypeName('Vector', [new TypeName('string')])]),
        'parameters' => null
      ])],
      $this->parse('new Filter<Vector<string>>();')
    );
  }

  /**
   * Test new
   *
   */
  #[@test]
  public function anonymousInstanceCreation() {
    $this->assertEquals(
      [new InstanceCreationNode([
        'type'       => new TypeName('Runnable'),
        'parameters' => null,
        'body'       => [
          new MethodNode([
            'modifiers'   => MODIFIER_PUBLIC,
            'annotations' => null,
            'name'        => 'run',
            'returns'     => TypeName::$VOID,
            'parameters'  => null,
            'throws'      => null,
            'body'        => [],
            'comment'     => null,
            'extension'   => null,
          ])
        ]
      ])],
      $this->parse('new Runnable() {
        public void run() {
          // TBI
        }
      };')
    );
  }

  /**
   * Test clone
   *
   */
  #[@test]
  public function cloningOperation() {
    $this->assertEquals(
      [new CloneNode(new VariableNode('b'))],
      $this->parse('clone $b;')
    );
  }

  /**
   * Test instanceof
   *
   */
  #[@test]
  public function instanceOfTest() {
    $this->assertEquals(
      [new InstanceOfNode([
        'expression' => new VariableNode('b'), 
        'type'       => new TypeName('XPClass')
      ])],
      $this->parse('$b instanceof XPClass;')
    );
  }

  /**
   * Test "new Object;" is not syntactically legal, this works in PHP
   * but is bascially the same as "new Object();"
   *
   */
  #[@test, @expect(FormatException::class)]
  public function newWithoutBraces() {
    $this->parse('new Object;');
  }
}
