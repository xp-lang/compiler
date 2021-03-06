<?php namespace net\xp_lang\tests\syntax\xp;

use text\parser\generic\ParseException;
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
    return (new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->imports;
  }

  /**
   * Test single-type import
   *
   */
  #[@test]
  public function singleTypeImport() {
    $this->assertEquals([new ImportNode([
        'name'     => 'util.collections.HashTable'
      ])], 
      $this->parse('import util.collections.HashTable; public class Test { }')
    );
  }

  /**
   * Test type-import-on-demand
   *
   */
  #[@test]
  public function typeImportOnDemand() {
    $this->assertEquals([new ImportNode([
        'name'     => 'util.collections.*'
      ])], 
      $this->parse('import util.collections.*; public class Test { }')
    );
  }

  /**
   * Test static import
   *
   */
  #[@test]
  public function staticImport() {
    $this->assertEquals([new StaticImportNode([
        'name'     => 'rdbms.criterion.Restrictions.in'
      ])], 
      $this->parse('import static rdbms.criterion.Restrictions::in; public class Test { }')
    );
  }

  /**
   * Test static import
   *
   */
  #[@test]
  public function staticImportOnDemand() {
    $this->assertEquals([new StaticImportNode([
        'name'     => 'rdbms.criterion.Restrictions.*'
      ])], 
      $this->parse('import static rdbms.criterion.Restrictions::*; public class Test { }')
    );
  }

  /**
   * Test static import
   *
   */
  #[@test]
  public function staticImportDeprecatedForm() {
    $this->assertEquals([new StaticImportNode([
        'name'     => 'rdbms.criterion.Restrictions.in'
      ])], 
      $this->parse('import static rdbms.criterion.Restrictions.in; public class Test { }')
    );
  }

  /**
   * Test native import
   *
   */
  #[@test]
  public function nativeImport() {
    $this->assertEquals([new NativeImportNode([
        'name'     => 'standard.*'
      ])], 
      $this->parse('import native standard.*; public class Test { }')
    );
  }

  /**
   * Test multiple imports
   *
   */
  #[@test]
  public function multipleImports() {
    $this->assertEquals([new ImportNode([
        'name'     => 'util.collections.*'
      ]), new ImportNode([
        'name'     => 'util.Date'
      ]), new ImportNode([
        'name'     => 'unittest.*'
      ])], 
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
  #[@test, @expect(ParseException::class)]
  public function noImportAll() {
    $this->parse('import *; public class Test { }');
  }

  /**
   * Test "import test" is not valid
   *
   */
  #[@test, @expect(ParseException::class)]
  public function noImportNothing() {
    $this->parse('import test; public class Test { }');
  }
}
