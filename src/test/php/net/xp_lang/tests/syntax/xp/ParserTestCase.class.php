<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;
use text\parser\generic\ParseException;

/**
 * Base class for all other parser test cases.
 */
abstract class ParserTestCase extends \unittest\TestCase {

  /**
   * Parse source code and return parse tree
   *
   * @param   string src Complete sourcecode
   * @return  xp.compiler.ast.ParseTree
   * @throws  lang.XPException In case of a parse error, the cause is raised
   */
  protected function parseTree($src) {
    try {
      return create(new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'));
    } catch (ParseException $e) {
      throw $e->getCause();
    }
  }

  /**
   * Parse method source and return statements inside this method.
   *
   * @param   string src Method sourcecode
   * @return  xp.compiler.ast.Node[]
   */
  protected function parse($src) {
    $tree= $this->parseTree('class Container {
      public void method() {
        '.$src.'
      }
    }');
    return $tree->declaration->body[0]->body;
  }
}
