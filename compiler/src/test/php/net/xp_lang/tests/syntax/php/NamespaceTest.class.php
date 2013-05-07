<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use xp\compiler\ast\PackageNode;
use xp\compiler\ast\ImportNode;

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
    return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'));
  }

  /**
   * Filters imports to return only import nodes, no native or static imports
   *
   * @param  xp.compiler.ast.ParseTree $tree
   * @return xp.compiler.ast.ImportNode[]
   */
  protected function importsIn(\xp\compiler\ast\ParseTree $tree) {
    return array_filter($tree->imports, function($import) {
      return $import instanceof ImportNode;
    });
  }

  #[@test]
  public function simple_namespace_declaration() {
    $this->assertEquals(
      new PackageNode(array('name' => 'demo')),
      $this->parse('<?php namespace demo; class A { }')->package
    );
  }

  #[@test]
  public function sub_namespace_declaration() {
    $this->assertEquals(
      new PackageNode(array('name' => 'demo.sub')),
      $this->parse('<?php namespace demo\\sub; class A { }')->package
    );
  }

  #[@test]
  public function sub_sub_namespace_declaration() {
    $this->assertEquals(
      new PackageNode(array('name' => 'demo.sub.child')),
      $this->parse('<?php namespace demo\\sub\\child; class A { }')->package
    );
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function absolute_namespace_not_allowed() {
    $this->parse('<?php namespace \\demo; class A { }');
  }

  #[@test]
  public function no_use_statements() {
    $this->assertEquals(
      array(),
      $this->importsIn($this->parse('<?php namespace demo;
        class A { }
      '))
    );
  }

  #[@test]
  public function single_use_statement() {
    $this->assertEquals(
      array(new ImportNode(array('name' => 'lang.Object'))),
      $this->importsIn($this->parse('<?php namespace demo;
        use lang\\Object;
        class A { }
      '))
    );
  }

  #[@test]
  public function two_use_statements() {
    $this->assertEquals(
      array(
        new ImportNode(array('name' => 'lang.Object')),
        new ImportNode(array('name' => 'util.Date'))
      ),
      $this->importsIn($this->parse('<?php namespace demo;
        use lang\\Object;
        use util\\Date;
        class A { }
      '))
    );
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function absolute_use_not_allowed() {
    $this->parse('<?php namespace demo; use \\lang\\Object; class A { }');
  }
}