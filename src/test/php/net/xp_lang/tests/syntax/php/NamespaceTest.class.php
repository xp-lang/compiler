<?php namespace net\xp_lang\tests\syntax\php; namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\PackageNode;
use xp\compiler\ast\ImportNode;
use text\parser\generic\ParseException;

/**
 * Test namespaces
 *
 * @see  php://namespaces
 */
class NamespaceTest extends ParserTestCase {

  /**
   * Parse source and return AST
   *
   * @param   string $src
   * @return  xp.compiler.ast.ParseTree
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'));
  }

  /**
   * Filters imports to return only import nodes, no native or static imports
   *
   * @param  xp.compiler.ast.ParseTree $tree
   * @return xp.compiler.ast.ImportNode[]
   */
  protected function importsIn(ParseTree $tree) {
    return array_filter($tree->imports, function($import) {
      return $import instanceof ImportNode;
    });
  }

  #[@test]
  public function simple_namespace_declaration() {
    $this->assertEquals(
      new PackageNode(['name' => 'demo']),
      $this->parse('<?php namespace demo; class A { }')->package
    );
  }

  #[@test]
  public function sub_namespace_declaration() {
    $this->assertEquals(
      new PackageNode(['name' => 'demo.sub']),
      $this->parse('<?php namespace demo\\sub; class A { }')->package
    );
  }

  #[@test]
  public function sub_sub_namespace_declaration() {
    $this->assertEquals(
      new PackageNode(['name' => 'demo.sub.child']),
      $this->parse('<?php namespace demo\\sub\\child; class A { }')->package
    );
  }

  #[@test, @expect(ParseException::class)]
  public function absolute_namespace_not_allowed() {
    $this->parse('<?php namespace \\demo; class A { }');
  }

  #[@test, @expect(ParseException::class)]
  public function double_namespace_declaration_not_allowed() {
    $this->parse('<?php namespace demo; namespace illegal; class A { }');
  }

  #[@test]
  public function no_use_statements() {
    $this->assertEquals(
      [],
      $this->importsIn($this->parse('<?php namespace demo;
        class A { }
      '))
    );
  }

  #[@test]
  public function single_use_statement() {
    $this->assertEquals(
      [new ImportNode(['name' => 'lang.Object'])],
      $this->importsIn($this->parse('<?php namespace demo;
        use lang\\Object;
        class A { }
      '))
    );
  }

  #[@test]
  public function two_use_statements() {
    $this->assertEquals(
      [
        new ImportNode(['name' => 'lang.Object']),
        new ImportNode(['name' => 'util.Date'])
      ],
      $this->importsIn($this->parse('<?php namespace demo;
        use lang\\Object;
        use util\\Date;
        class A { }
      '))
    );
  }

  #[@test, @expect(ParseException::class)]
  public function absolute_use_not_allowed() {
    $this->parse('<?php namespace demo; use \\lang\\Object; class A { }');
  }
}