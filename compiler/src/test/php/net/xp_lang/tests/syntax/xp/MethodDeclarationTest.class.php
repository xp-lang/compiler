<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\OperatorNode;
use xp\compiler\ast\AnnotationNode;
use xp\compiler\types\TypeName;

/**
 * TestCase
 *
 */
class MethodDeclarationTest extends ParserTestCase {

  /**
   * Parse method source and return statements inside this method.
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function toStringMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'toString',
      'returns'    => new TypeName('string'),
      'parameters' => null,
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Null { 
      public string toString() { }
    }'));
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function equalsMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('bool'),
      'parameters' => array(array(
        'name'  => 'cmp',
        'type'  => new TypeName('Object'),
        'check' => true
      )),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Null { 
      public bool equals(Object $cmp) { }
    }'));
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function abstractMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_ABSTRACT,
      'annotations'=> null,
      'name'       => 'setTrace',
      'returns'    => TypeName::$VOID,
      'parameters' => array(array(
        'name'  => 'cat',
        'type'  => new TypeName('util.log.LogCategory'),
        'check' => true
      )),
      'throws'     => null,
      'body'       => null,
      'extension'  => null
    ))), $this->parse('class Null { 
      public abstract void setTrace(util.log.LogCategory $cat);
    }'));
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function interfaceMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'compareTo',
      'returns'    => new TypeName('int'),
      'parameters' => array(array(
        'name'  => 'other',
        'type'  => new TypeName('Object'),
        'check' => true
      )),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('interface Comparable { 
      public int compareTo(Object $other) { }
    }'));
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function staticMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'name'       => 'loadClass',
      'returns'    => new TypeName('Class', array(new TypeName('T'))),
      'parameters' => array(array(
        'name'  => 'name',
        'type'  => new TypeName('string'),
        'check' => true
      )),
      'throws'     => array(new TypeName('ClassNotFoundException'), new TypeName('SecurityException')),
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Class<T> { 
      public static Class<T> loadClass(string $name) throws ClassNotFoundException, SecurityException { }
    }'));
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function printfMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations' => null,
      'name'        => 'printf',
      'returns'     => new TypeName('string'),
      'parameters'  => array(array(
        'name'      => 'format',
        'type'      => new TypeName('string'),
        'check'     => true
      ), array(
        'name'      => 'args',
        'type'      => new TypeName('string'),
        'vararg'    => true,
        'check'     => true
      )), 
      'throws'      => null,
      'body'        => array(),
      'extension'   => null
    ))), $this->parse('class Format { 
      public static string printf(string $format, string... $args) {
      
      }
    }'));
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function addAllMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'addAll',
      'returns'    => TypeName::$VOID,
      'parameters' => array(array(
        'name'   => 'elements',
        'type'   => new TypeName('T[]'),  // XXX FIXME this is probably not a good representation
        'check'  => true      
      )), 
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class List { 
      public void addAll(T[] $elements) { }
    }'));
  }

  /**
   * Test operator declaration
   *
   */
  #[@test]
  public function plusOperator() {
    $this->assertEquals(array(new OperatorNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'symbol'     => '+',
      'returns'    => new TypeName('self'),
      'parameters' => array(array(
        'name'  => 'a',
        'type'  => new TypeName('self'),
        'check' => true
      ), array(
        'name'  => 'b',
        'type'  => new TypeName('self'),
        'check' => true
      )),
      'throws'     => null,
      'body'       => array()
    ))), $this->parse('class Integer { 
      public static self operator + (self $a, self $b) { }
    }'));
  }

  /**
   * Test missing return type yields a parse error
   *
   */
  #[@test, @expect('text.parser.generic.ParseException')]
  public function missingReturnType() {
    $this->parse('class Broken { public run() { }}');
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function noRuntimeTypeCheck() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('bool'),
      'parameters' => array(array(
        'name'  => 'cmp',
        'type'  => new TypeName('Generic'),
        'check' => false
      )),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Test { 
      public bool equals(Generic? $cmp) { }
    }'));
  }

  /**
   * Test method declaration
   *
   */
  #[@test]
  public function mapMethodWithAnnotations() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => 0,
      'annotations'=> array(
        new AnnotationNode(array(
          'type'        => 'test',
          'parameters'  => array()
        ))
      ),
      'name'       => 'map',
      'returns'    => new TypeName('[:string]'),
      'parameters' => array(), 
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Any { 
      [@test] [:string] map() { }
    }'));
  }
}
