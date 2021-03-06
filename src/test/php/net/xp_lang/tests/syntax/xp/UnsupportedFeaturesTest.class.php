<?php namespace net\xp_lang\tests\syntax\xp;

use lang\FormatException;

/**
 * TestCase showing what is not supported in XP language in comparison
 * to PHP.
 *
 */
class UnsupportedFeaturesTest extends ParserTestCase {

  /**
   * Test variable variables are not supported
   *
   * @see   php://language.variables.variable
   */
  #[@test, @expect(FormatException::class)]
  public function variableVariables() {
    $this->parse('$$i= 0;');
  }

  /**
   * Test dynamic variables are not supported
   *
   * @see   php://language.variables.variable
   */
  #[@test, @expect(FormatException::class)]
  public function dynamicVariables() {
    $this->parse('${$i}= 0;');
  }

  /**
   * Test goto is not supported
   *
   * @see   php://goto
   */
  #[@test, @expect(FormatException::class)]
  public function gotoStatement() {
    $this->parse('goto error;');
  }

  /**
   * Test declare is not supported
   *
   * @see   php://declare
   */
  #[@test, @expect(FormatException::class)]
  public function declareStatement() {
    $this->parse('declare(ticks=1) { }');
  }

  /**
   * Test functions are not supported
   *
   * @see   php://language.functions
   */
  #[@test, @expect(FormatException::class)]
  public function functions() {
    $this->parse('function a() { }');
  }

  /**
   * Test new statement without braces
   *
   * @see   php://new
   */
  #[@test, @expect(FormatException::class)]
  public function newWithoutBraces() {
    $this->parse('new A;');
  }

  /**
   * Test references are not supported
   *
   * @see   php://language.references
   */
  #[@test, @expect(FormatException::class)]
  public function references() {
    $this->parse('$a= &$b;');
  }

  /**
   * Test "elseif" keyword is not supported
   *
   * @see   php://elseif
   */
  #[@test, @expect(FormatException::class)]
  public function elseifKeyword() {
    $this->parse('if ($a) { $b++; } elseif ($c) { $d++; }');
  }

  /**
   * Test "include" keyword is not supported without braces
   *
   * @see   php://include
   */
  #[@test, @expect(FormatException::class)]
  public function includeKeywordWithoutBraces() {
    $this->parse('include "functions.inc";');
  }

  /**
   * Test "require" keyword is not supported without braces
   *
   * @see   php://require
   */
  #[@test, @expect(FormatException::class)]
  public function requireKeywordWithoutBraces() {
    $this->parse('require "functions.inc";');
  }

  /**
   * Test "echo" keyword is not supported without braces
   *
   * @see   php://echo
   */
  #[@test, @expect(FormatException::class)]
  public function echoKeywordWithoutBraces() {
    $this->parse('echo "Hello";');
  }

  /**
   * Test alternative syntax for control structures are not supported
   *
   * @see   php://control-structures.alternative-syntax
   */
  #[@test, @expect(FormatException::class)]
  public function alternativeIf() {
    $this->parse('if ($a): $b++; endif;');
  }

  /**
   * Test alternative syntax for control structures are not supported
   *
   * @see   php://control-structures.alternative-syntax
   */
  #[@test, @expect(FormatException::class)]
  public function alternativeWhile() {
    $this->parse('while ($a > 0): $a--; endwhile;');
  }

  /**
   * Test alternative syntax for control structures are not supported
   *
   * @see   php://control-structures.alternative-syntax
   */
  #[@test, @expect(FormatException::class)]
  public function alternativeFor() {
    $this->parse('for ($i= 0; $i < 4; $i++): $b--; endfor;');
  }

  /**
   * Test alternative syntax for control structures are not supported
   *
   * @see   php://control-structures.alternative-syntax
   */
  #[@test, @expect(FormatException::class)]
  public function alternativeForeach() {
    $this->parse('foreach ($a in $list): $b--; endforeach;');
  }

  /**
   * Test silence operator is not supported
   *
   * @see   php://language.operators.errorcontrol
   */
  #[@test, @expect(FormatException::class)]
  public function silenceOperator() {
    $this->parse('$a= @$b;');
  }

  /**
   * Test execution operator is not supported
   *
   * @see   php://language.operators.execution
   */
  #[@test, @expect(FormatException::class)]
  public function executionOperator() {
    $this->parse('$a= `ls -al`;');
  }

  /**
   * Test inline HTML is not supported
   *
   * @see   php://language.basic-syntax.phpmode
   */
  #[@test, @expect(FormatException::class)]
  public function inlineHTML() {
    $this->parse('?>HTML<?php namespace net\xp_lang\tests\syntax\xp;');
  }

  /**
   * Test hash (#) comment is not supported
   *
   * @see   php://language.basic-syntax.comments
   */
  #[@test, @expect(FormatException::class)]
  public function hashComment() {
    $this->parse('# $a= 1;');
  }

  /**
   * Test heredoc is not supported
   *
   * @see   php://heredoc
   */
  #[@test, @expect(FormatException::class)]
  public function hereDoc() {
    $this->parse("\$s= <<<EOS\nHello\nEOS;");
  }

  /**
   * Test nowdoc is not supported
   *
   * @see   php://nowdoc
   */
  #[@test, @expect(FormatException::class)]
  public function nowDoc() {
    $this->parse("\$s= <<<'EOS'\nHello\nEOS;");
  }

  /**
   * Test namespaced constants are not supported
   *
   * @see   php://namespaces
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Syntax error/')]
  public function namespacedConstants() {
    $this->parse('$s= hello\world;');
  }

  /**
   * Test namespaced functions are not supported
   *
   * @see   php://namespaces
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Syntax error/')]
  public function namespacedFunctions() {
    $this->parse('$s= hello\world();');
  }

  /**
   * Test namespaced instantiations are not supported
   *
   * @see   php://namespaces
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Syntax error/')]
  public function namespacedInstantiation() {
    $this->parse('$s= new hello\World();');
  }

  /**
   * Test namespaced static member accesses are not supported
   *
   * @see   php://namespaces
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= '/Syntax error/')]
  public function namespacedStaticMemberAccess() {
    $this->parse('$s= hello\World::class;');
  }
}
