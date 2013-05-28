<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Lexer;
use xp\compiler\syntax\xp\Parser;

/**
 * Base class for all other parser test cases.
 *
 */
abstract class ParserTestCase extends \unittest\TestCase {

  /**
   * Parse method source and return statements inside this method.
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    try {
      return create(new Parser())->parse(new Lexer('class Container {
        public void method() {
          '.$src.'
        }
      }', '<string:'.$this->name.'>'))->declaration->body[0]->body;
    } catch (\text\parser\generic\ParseException $e) {
      throw $e->getCause();
    }
  }
}
