<?php namespace net\xp_lang\tests\types;

use xp\compiler\types\TypeReference;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;

class TypeReferenceTest extends \unittest\TestCase {

  #[@test]
  public function nameWithoutPackage() {
    $decl= new TypeReference(new TypeName('TestCase'));
    $this->assertEquals('TestCase', $decl->name());
  }

  #[@test]
  public function literalWithoutPackage() {
    $decl= new TypeReference(new TypeName('TestCase'));
    $this->assertEquals('TestCase', $decl->literal());
  }

  #[@test]
  public function literalWithoutPackageAndPackageModifier() {
    $decl= new TypeReference(new TypeName('TestCase'), Types::PARTIAL_KIND, MODIFIER_PACKAGE);
    $this->assertEquals('TestCase', $decl->literal());
  }

  #[@test]
  public function nameWithPackage() {
    $decl= new TypeReference(new TypeName('unittest.TestCase'));
    $this->assertEquals('unittest.TestCase', $decl->name());
  }

  #[@test]
  public function literalWithPackage() {
    $decl= new TypeReference(new TypeName('unittest.TestCase'));
    $this->assertEquals('TestCase', $decl->literal());
  }

  #[@test]
  public function literalWithPackageAndPackageModifier() {
    $decl= new TypeReference(new TypeName('unittest.TestCase'), Types::PARTIAL_KIND, MODIFIER_PACKAGE);
    $this->assertEquals('TestCase', $decl->literal());
  }

  #[@test]
  public function intIsNotEnumerable() {
    $decl= new TypeReference(new TypeName('int'));
    $this->assertFalse($decl->isEnumerable());
  }

  #[@test]
  public function arrayIsEnumerable() {
    $decl= new TypeReference(new TypeName('int[]'));
    $this->assertTrue($decl->isEnumerable());
  }

  #[@test]
  public function mapIsEnumerable() {
    $decl= new TypeReference(new TypeName('[:string]'));
    $this->assertTrue($decl->isEnumerable());
  }

  #[@test]
  public function arrayEnumerator() {
    $enum= (new TypeReference(new TypeName('int[]')))->getEnumerator();
    $this->assertEquals(new TypeName('int'), $enum->key);
    $this->assertEquals(new TypeName('int'), $enum->value);
  }

  #[@test]
  public function mapEnumerator() {
    $enum= (new TypeReference(new TypeName('[:string]')))->getEnumerator();
    $this->assertEquals(new TypeName('string'), $enum->key);
    $this->assertEquals(new TypeName('string'), $enum->value);
  }

  #[@test]
  public function intDoesNotHaveAnIndexer() {
    $decl= new TypeReference(new TypeName('int'));
    $this->assertFalse($decl->hasIndexer());
  }

  #[@test]
  public function arrayHasIndexer() {
    $decl= new TypeReference(new TypeName('int[]'));
    $this->assertTrue($decl->hasIndexer());
  }

  #[@test]
  public function mapHasIndexer() {
    $decl= new TypeReference(new TypeName('[:string]'));
    $this->assertTrue($decl->hasIndexer());
  }

  #[@test]
  public function arrayIndexer() {
    $indexer= (new TypeReference(new TypeName('int[]')))->getIndexer();
    $this->assertEquals(new TypeName('int'), $indexer->type);
    $this->assertEquals(new TypeName('int'), $indexer->parameter);
  }

  #[@test]
  public function mapIndexer() {
    $indexer= (new TypeReference(new TypeName('[:string]')))->getIndexer();
    $this->assertEquals(new TypeName('string'), $indexer->type);
    $this->assertEquals(new TypeName('string'), $indexer->parameter);
  }
}
