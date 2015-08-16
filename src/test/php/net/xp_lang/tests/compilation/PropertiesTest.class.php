<?php namespace net\xp_lang\tests\compilation;

use xp\compiler\emit\php\V54Emitter;
use xp\compiler\types\TypeName;
use xp\compiler\types\TypeReflection;
use xp\compiler\types\Property;
use xp\compiler\types\TaskScope;
use xp\compiler\io\FileManager;
use xp\compiler\io\FileSource;
use xp\compiler\task\CompilationTask;
use xp\compiler\diagnostic\NullDiagnosticListener;
use xp\compiler\Syntax;
use io\File;
use io\streams\MemoryInputStream;
use lang\reflect\Modifiers;

/**
 * TestCase
 *
 */
 class PropertiesTest extends \unittest\TestCase {
  protected $scope;
  protected $emitter;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->emitter= new V54Emitter();
    $this->scope= new TaskScope(new CompilationTask(
      new FileSource(new File(__FILE__), Syntax::forName('xp')),
      new NullDiagnosticListener(),
      new FileManager(),
      $this->emitter
    ));
  }

  /**
   * Compile class from source and return compiled type
   *
   * @param   string src
   * @return  xp.compiler.types.TypeReflection
   */
  protected function compile($src) {
    $unique= 'FixtureClassFor'.$this->getClass()->getSimpleName().ucfirst($this->name);
    $r= $this->emitter->emit(
      Syntax::forName('xp')->parse(new MemoryInputStream(sprintf($src, $unique))),
      $this->scope
    );
    $r->executeWith(array());
    return new TypeReflection(\lang\XPClass::forName($r->type()->name()));
  }

  protected function assertProperty($modifiers, $name, $type, $actual) {
    $this->assertEquals(
      array('modifiers' => Modifiers::namesOf($modifiers), 'name' => $name, 'type' => $type),
      array('modifiers' => Modifiers::namesOf($actual->modifiers), 'name' => $actual->name, 'type' => $actual->type)
    );
  }

  #[@test]
  public function no_properties() {
    $this->assertEquals(
      false, 
      $this->compile('class %s { }')->hasProperty('irrelevant')
    );
  }

  #[@test]
  public function property_with_get_exists() {
    $this->assertEquals(
      true, 
      $this->compile('class %s { public int length { get; } }')->hasProperty('length')
    );
  }

  #[@test]
  public function property_with_get() {
    $this->assertProperty(
      MODIFIER_PUBLIC, 'length', new TypeName('int'),
      $this->compile('class %s { public int length { get; } }')->getProperty('length')
    );
  }

  #[@test]
  public function property_with_set_exists() {
    $this->assertEquals(
      true, 
      $this->compile('class %s { public string name { set; } }')->hasProperty('name')
    );
  }

  #[@test]
  public function property_with_set() {
    $this->assertProperty(
      MODIFIER_PUBLIC, 'name', new TypeName('string'),
      $this->compile('class %s { public string name { set; } }')->getProperty('name')
    );
  }

  #[@test]
  public function property_with_get_and_set_exists() {
    $this->assertEquals(
      true, 
      $this->compile('class %s { public lang.types.Bytes buffer { get; set; } }')->hasProperty('buffer')
    );
  }

  #[@test]
  public function property_with_get_and_set() {
    $this->assertProperty(
      MODIFIER_PUBLIC, 'buffer', new TypeName('lang.types.Bytes'),
      $this->compile('class %s { public lang.types.Bytes buffer { get; set; } }')->getProperty('buffer')
    );
  }

  #[@test]
  public function non_existant_property_does_not_exist() {
    $this->assertEquals(
      false, 
      $this->compile('class %s { public int length { get; } }')->hasProperty('@@non-existant@@')
    );
  }

  #[@test]
  public function non_existant_property() {
    $this->assertEquals(
      null, 
      $this->compile('class %s { public int length { get; } }')->getProperty('@@non-existant@@')
    );
  }
}
