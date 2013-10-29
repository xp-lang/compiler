<?php namespace net\xp_lang\tests\compilation;

use xp\compiler\emit\php\V53Emitter;
use xp\compiler\types\TypeName;
use xp\compiler\types\Parameter;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\task\CompilationTask;
use xp\compiler\task\FileArgument;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\Syntax;
use io\File;
use io\streams\MemoryInputStream;

/**
 * TestCase
 *
 * @see   xp://xp.compiler.types.CompiledType
 */
class TypeTest extends \unittest\TestCase {
  protected $scope;
  protected $emitter;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->emitter= new V53Emitter();
    $this->scope= new TaskScope(new CompilationTask(
      new FileArgument(new File(__FILE__), Syntax::forName('xp')),
      new NullDiagnosticListener(),
      new FileManager(),
      $this->emitter
    ));
  }

  /**
   * Compile class from source and return compiled type
   *
   * @param   string src
   * @return  xp.compiler.types.Types
   */
  protected function compile($src) {
    $r= $this->emitter->emit(
      Syntax::forName('xp')->parse(new MemoryInputStream($src)),
      $this->scope
    );
    return $r->type();
  }

  #[@test]
  public function name() {
    $this->assertEquals('Person', $this->compile('class Person { }')->name());
  }

  #[@test]
  public function nameInsidePackage() {
    $this->assertEquals('demo.Person', $this->compile('package demo; class Person { }')->name());
  }

  #[@test]
  public function packageNameInsidePackage() {
    $this->assertEquals('demo.Person', $this->compile('package demo; package class Person { }')->name());
  }

  #[@test]
  public function literal() {
    $this->assertEquals('Person', $this->compile('class Person { }')->literal());
  }

  #[@test]
  public function classFieldExists() {
    $t= $this->compile('class Person { public string $name; }');
    $this->assertTrue($t->hasField('name'));
  }

  #[@test]
  public function classField() {
    $f= $this->compile('class Person { public string $name; }')->getField('name');
    $this->assertEquals('name', $f->name);
    $this->assertEquals(new TypeName('string'), $f->type);
    $this->assertEquals(MODIFIER_PUBLIC, $f->modifiers);
  }

  #[@test]
  public function classPropertyExists() {
    $t= $this->compile('class Person { public string name { get { } set { } } }');
    $this->assertTrue($t->hasProperty('name'));
  }

  #[@test]
  public function classProperty() {
    $f= $this->compile('class Person { public string name { get { } set { } } }')->getProperty('name');
    $this->assertEquals('name', $f->name);
    $this->assertEquals(new TypeName('string'), $f->type);
    $this->assertEquals(MODIFIER_PUBLIC, $f->modifiers);
  }

  #[@test]
  public function classStaticFieldExists() {
    $t= $this->compile('class Logger { public static self $instance; }');
    $this->assertTrue($t->hasField('instance'));
  }

  #[@test]
  public function classStaticField() {
    $f= $this->compile('class Logger { public static self $instance; }')->getField('instance');
    $this->assertEquals('instance', $f->name);
    $this->assertEquals(new TypeName('Logger'), $f->type);
    $this->assertEquals(MODIFIER_STATIC | MODIFIER_PUBLIC, $f->modifiers);
  }

  #[@test]
  public function classStaticFieldWithNonStaticInitialization() {
    $f= $this->compile('class Convert { public static var $headline= text.regex.Pattern::compile("==(.+)=="); }')->getField('headline');
    $this->assertEquals('headline', $f->name);
    $this->assertEquals(new TypeName('text.regex.Pattern'), $f->type);
    $this->assertEquals(MODIFIER_STATIC | MODIFIER_PUBLIC, $f->modifiers);
  }

  #[@test]
  public function classFieldWithNonStaticInitialization() {
    $f= $this->compile('class Convert { public var $headline= text.regex.Pattern::compile("==(.+)=="); }')->getField('headline');
    $this->assertEquals('headline', $f->name);
    $this->assertEquals(new TypeName('text.regex.Pattern'), $f->type);
    $this->assertEquals(MODIFIER_PUBLIC, $f->modifiers);
  }
  
  #[@test]
  public function enumFieldExists() {
    $t= $this->compile('enum Days { MON, TUE, WED, THU, FRI, SAT, SUN }');
    $this->assertTrue($t->hasField('MON'));
  }

  #[@test]
  public function enumField() {
    $f= $this->compile('enum Days { MON, TUE, WED, THU, FRI, SAT, SUN }')->getField('MON');
    $this->assertEquals('MON', $f->name);
    $this->assertEquals(new TypeName('Days'), $f->type);
    $this->assertEquals(MODIFIER_STATIC | MODIFIER_PUBLIC, $f->modifiers);
  }

  #[@test]
  public function classConstantExists() {
    $t= $this->compile('class StringConstants { const string LF= "\n"; }');
    $this->assertTrue($t->hasConstant('LF'));
  }

  #[@test]
  public function classConstant() {
    $c= $this->compile('class StringConstants { const string LF= "\n"; }')->getConstant('LF');
    $this->assertEquals('LF', $c->name);
    $this->assertEquals(new TypeName('string'), $c->type);
    $this->assertEquals("\n", $c->value);
  }

  #[@test]
  public function interfaceConstantExists() {
    $t= $this->compile('interface StringConstants { const string LF= "\n"; }');
    $this->assertTrue($t->hasConstant('LF'));
  }

  #[@test]
  public function interfaceConstant() {
    $c= $this->compile('interface StringConstants { const string LF= "\n"; }')->getConstant('LF');
    $this->assertEquals('LF', $c->name);
    $this->assertEquals(new TypeName('string'), $c->type);
    $this->assertEquals("\n", $c->value);
  }

  #[@test]
  public function classMethodExists() {
    $t= $this->compile('class String { public self substring(int $start, int $len) { }}');
    $this->assertTrue($t->hasMethod('substring'));
  }

  #[@test]
  public function classMethod() {
    $m= $this->compile('class String { public self substring(int $start, int $len) { }}')->getMethod('substring');
    $this->assertEquals('substring', $m->name);
    $this->assertEquals(new TypeName('String'), $m->returns);
    $this->assertEquals(MODIFIER_PUBLIC, $m->modifiers);
    $this->assertEquals(
      array(new Parameter('start', new TypeName('int')), new Parameter('len', new TypeName('int'))),
      $m->parameters
    );
  }

  #[@test]
  public function classOperatorExists() {
    $t= $this->compile('class Complex { public static self operator + (self $a, self $b) { }}');
    $this->assertTrue($t->hasOperator('+'));
  }

  #[@test]
  public function classOperator() {
    $m= $this->compile('class Complex { public static self operator + (self $a, self $b) { }}')->getOperator('+');
    $this->assertEquals('+', $m->symbol);
    $this->assertEquals(new TypeName('Complex'), $m->returns);
    $this->assertEquals(MODIFIER_PUBLIC | MODIFIER_STATIC, $m->modifiers);
    $this->assertEquals(
      array(new Parameter('a', new TypeName('Complex')), new Parameter('b', new TypeName('Complex'))),
      $m->parameters
    );
  }

  #[@test]
  public function enumMethodExists() {
    $t= $this->compile('enum Coin { penny(1), nickel(2), dime(10), quarter(25); public string color() { }}');
    $this->assertTrue($t->hasMethod('color'));
  }

  #[@test]
  public function enumMethod() {
    $m= $this->compile('enum Coin { penny(1), nickel(2), dime(10), quarter(25); public string color() { }}')->getMethod('color');
    $this->assertEquals('color', $m->name);
    $this->assertEquals(new TypeName('string'), $m->returns);
    $this->assertEquals(MODIFIER_PUBLIC, $m->modifiers);
    $this->assertEquals(array(), $m->parameters);
  }

  #[@test]
  public function classIndexerExists() {
    $t= $this->compile('class ArrayList<T> { public T this[int $offset] { get { } set { } isset { } unset { } }}');
    $this->assertTrue($t->hasIndexer('color'));
  }

  #[@test]
  public function classIndexer() {
    $i= $this->compile('class ArrayList<T> { public T this[int $offset] { get { } set { } isset { } unset { } }}')->getIndexer();
    $this->assertEquals(new TypeName('T'), $i->type);
    $this->assertEquals(new TypeName('int'), $i->parameter);
  }
}
