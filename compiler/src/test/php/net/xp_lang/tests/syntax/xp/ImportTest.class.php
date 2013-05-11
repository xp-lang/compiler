<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use xp\compiler\ast\ImportNode;
use xp\compiler\ast\StaticImportNode;
use xp\compiler\ast\NativeImportNode;

/**
 * TestCase for import statements
 */
class ImportTest extends ParserTestCase {

  /**
   * Parse method source and return statements inside this method.
   *
   * @param   string src
   * @return  xp.compiler.Node
   */
  protected function parse($src) {
    return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->imports;
  }

  /**
   * Test single-type import
   *
   */
  #[@test]
  public function singleTypeImport() {
    $this->assertEquals(array(new ImportNode(array(
        'name'     => 'util.collections.HashTable'
      ))), 
      $this->parse('import util.collections.HashTable; public class Test { }')
    );
  }

  /**
   * Test type-import-on-demand
   *
   */
  #[@test]
  public function typeImportOnDemand() {
    $this->assertEquals(array(new ImportNode(array(
        'name'     => 'util.collections.*'
      ))), 
      $this->parse('import util.collections.*; public class Test { }')
    );
  }

  /**
   * Test static import
   *
   */
  #[@test]
  public function staticImport() {
    $this->assertEquals(array(new StaticImportNode(array(
        'name'     => 'rdbms.criterion.Restrictions.*'
      ))), 
      $this->parse('import static rdbms.criterion.Restrictions.*; public class Test { }')
    );
  }

  /**
   * Test native import
   *
   */
  #[@test]
  public function nativeImport() {
    $this->assertEquals(array(new NativeImportNode(array(
        'name'     => 'standard.*'
      ))), 
      $this->parse('import native standard.*; public class Test { }')
    );
  }

  /**
   * Test multiple imports
   *
   */
  #[@test]
  public function multipleImports() {
    $this->assertEquals(array(new ImportNode(array(
        'name'     => 'util.collections.*'
      )), new ImportNode(array(
        'name'     => 'util.Date'
      )), new ImportNode(array(
        'name'     => 'unittest.*'
      ))), 
      $this->parse('
        import util.collections.*; 
        import util.Date; 
        import unittest.*; 

        public class Test { }
      ')
    );
  }

  /**
   * Test "import *" is not valid
   *
   */
  #[@test, @expect('text.parser.generic.ParseException')]
  public function noImportAll() {
    $this->parse('import *; public class Test { }');
  }

  /**
   * Test "import test" is not valid
   *
   */
  #[@test, @expect('text.parser.generic.ParseException')]
  public function noImportNothing() {
    $this->parse('import test; public class Test { }');
  }
}
