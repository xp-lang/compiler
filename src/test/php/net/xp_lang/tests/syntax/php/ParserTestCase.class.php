<?php namespace net\xp_lang\tests\syntax\php;

use xp\compiler\syntax\php\Parser;
use xp\compiler\syntax\php\Lexer;
use text\parser\generic\ParseException;

/**
 * Base class for all other parser test cases.
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
      return (new Parser())->parse(new Lexer('<?php class Container {
        public function method() {
          '.$src.'
        }
      }', '<string:'.$this->name.'>'))->declaration->body[0]->body;
    } catch (ParseException $e) {
      throw $e->getCause();
    }
  }
}
