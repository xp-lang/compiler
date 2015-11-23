<?php namespace net\xp_lang\tests\syntax\xp;

use xp\compiler\syntax\xp\Parser;
use xp\compiler\syntax\xp\Lexer;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\UnpackNode;
use xp\compiler\types\TypeName;

class VariadicTest extends ParserTestCase {

  /**
   * Parse class source and return method declaration
   *
   * @param   string src
   * @return  xp.compiler.Node[]
   */
  protected function parse($src) {
    return (new Parser())->parse(new Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
  }

  #[@test]
  public function logger_example() {
    $this->assertEquals(
      [new MethodNode([
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> null,
        'name'       => 'warn',
        'returns'    => new TypeName('void'),
        'parameters' => [
          ['name' => 'args', 'type' => new TypeName('var'), 'check' => false, 'vararg' => true]
        ],
        'throws'     => null,
        'body'       => [new MethodCallNode(new VariableNode('this'), 'log', [
          new StringNode('warn'),
          new UnpackNode(new VariableNode('args'))
        ])],
        'extension'  => null
      ])],
      $this->parse('class Logger {
        public void warn(var?... $args) {
          $this.log("warn", ...$args);
        }
      }')
    );
  }
}