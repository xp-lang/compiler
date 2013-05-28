<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\CastNode;
use xp\compiler\ast\BooleanNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class CastingTest extends ParserTestCase {

  /**
   * Test prefix notation
   *
   */
  #[@test, @ignore('Prefix casting unsupported')]
  public function prefixIntCast() {
    $this->assertEquals(
      array(new AssignmentNode(array(
        'variable'    => new VariableNode('a'),
        'expression'  => new CastNode(array(
          'type'        => new TypeName('int'),
          'expression'  => new VariableNode('b')
        )),
        'op'          => '='
      ))),
      $this->parse('$a= (int)$b;')
    );
  }

  /**
   * Test prefix notation
   *
   */
  #[@test, @ignore('Prefix casting unsupported')]
  public function prefixIntCastBracketedLiteral() {
    $this->assertEquals(
      array(new AssignmentNode(array(
        'variable'    => new VariableNode('a'),
        'expression'  => new CastNode(array(
          'type'        => new TypeName('int'),
          'expression'  => new BooleanNode(true)
        )),
        'op'          => '='
      ))),
      $this->parse('$a= (int)(true);')
    );
  }

  /**
   * Test prefix notation
   *
   */
  #[@test, @ignore('Prefix casting unsupported')]
  public function prefixIntArrayCast() {
    $this->assertEquals(
      array(new AssignmentNode(array(
        'variable'    => new VariableNode('a'),
        'expression'  => new CastNode(array(
          'type'        => new TypeName('int[]'),
          'expression'  => new VariableNode('b')
        )),
        'op'          => '='
      ))),
      $this->parse('$a= (int[])$b;')
    );
  }

  /**
   * Test prefix notation
   *
   */
  #[@test, @ignore('Prefix casting unsupported')]
  public function prefixGenericCast() {
    $this->assertEquals(
      array(new AssignmentNode(array(
        'variable'    => new VariableNode('a'),
        'expression'  => new CastNode(array(
          'type'        => new TypeName('List', array(new TypeName('String'))),
          'expression'  => new VariableNode('b')
        )),
        'op'          => '='
      ))),
      $this->parse('$a= (List<String>)$b;')
    );
  }

  /**
   * Test prefix notation
   *
   */
  #[@test, @ignore('Prefix casting unsupported')]
  public function prefixQualifiedCast() {
    $this->assertEquals(
      array(new AssignmentNode(array(
        'variable'    => new VariableNode('a'),
        'expression'  => new CastNode(array(
          'type'        => new TypeName('com.example.bank.Account'),
          'expression'  => new VariableNode('b')
        )),
        'op'          => '='
      ))),
      $this->parse('$a= (com.example.bank.Account)$b;')
    );
  }

  /**
   * Test "as" notation
   *
   */
  #[@test]
  public function postfixQualifiedCast() {
    $this->assertEquals(
      array(new AssignmentNode(array(
        'variable'    => new VariableNode('a'),
        'expression'  => new CastNode(array(
          'type'        => new TypeName('com.example.bank.Account'),
          'check'       => true,
          'expression'  => new VariableNode('b')
        )),
        'op'          => '='
      ))),
      $this->parse('$a= $b as com.example.bank.Account;')
    );
  }

  /**
   * Test "as" notation
   *
   */
  #[@test]
  public function postfixQualifiedNonEnforcedCast() {
    $this->assertEquals(
      array(new AssignmentNode(array(
        'variable'    => new VariableNode('a'),
        'expression'  => new CastNode(array(
          'type'        => new TypeName('com.example.bank.Account'),
          'check'       => false,
          'expression'  => new VariableNode('b')
        )),
        'op'          => '='
      ))),
      $this->parse('$a= $b as com.example.bank.Account?;')
    );
  }
}
