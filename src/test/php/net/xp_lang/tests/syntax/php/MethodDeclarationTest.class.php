<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\AnnotationNode;
use xp\compiler\types\TypeName;

class MethodDeclarationTest extends ParserTestCase {

  /**
   * Parse method source and return statements inside this method.
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer('<?php '.$src.'?>', '<string:'.$this->name.'>'))->declaration->body;
  }

  #[@test]
  public function toStringMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'toString',
      'returns'    => new TypeName('var'),
      'parameters' => null,
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class null { 
      public function toString() { }
    }'));
  }

  #[@test]
  public function toStringMethodWithReturnType() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'toString',
      'returns'    => new TypeName('string'),
      'parameters' => null,
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class null { 
      public function toString() : string { }
    }'));
  }

  #[@test]
  public function equalsMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('var'),
      'parameters' => array(array(
        'name'  => 'cmp',
        'type'  => new TypeName('Object'),
        'check' => true
      )),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class null { 
      public function equals(Object $cmp) { }
    }'));
  }

  #[@test]
  public function abstractMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_ABSTRACT,
      'annotations'=> null,
      'name'       => 'setTrace',
      'returns'    => new TypeName('var'),
      'parameters' => array(array(
        'name'  => 'cat',
        'type'  => new TypeName('LogCategory'),
        'check' => true
      )),
      'throws'     => null,
      'body'       => null,
      'extension'  => null
    ))), $this->parse('class null { 
      public abstract function setTrace(LogCategory $cat);
    }'));
  }

  #[@test]
  public function interfaceMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'compareTo',
      'returns'    => new TypeName('var'),
      'parameters' => array(array(
        'name'  => 'other',
        'type'  => new TypeName('Object'),
        'check' => true
      )),
      'throws'     => null,
      'body'       => null,
      'extension'  => null
    ))), $this->parse('interface Comparable { 
      public function compareTo(Object $other);
    }'));
  }

  #[@test]
  public function addAllMethod() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'addAll',
      'returns'    => new TypeName('var'),
      'parameters' => array(array(
        'name'   => 'elements',
        'type'   => new TypeName('var[]'),
        'check'  => true      
      )), 
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class List { 
      public function addAll(array $elements) { }
    }'));
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function missingFunctionKeyword() {
    $this->parse('class Broken { public run() { }}');
  }

  #[@test]
  public function noRuntimeTypeCheck() {
    $this->assertEquals(array(new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('var'),
      'parameters' => array(array(
        'name'  => 'cmp',
        'type'  => new TypeName('var'),
        'check' => false
      )),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Test { 
      public function equals($cmp) { }
    }'));
  }

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
      'returns'    => new TypeName('var'),
      'parameters' => array(), 
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ))), $this->parse('class Any { 
      #[@test]
      function map() { }
    }'));
  }
}
