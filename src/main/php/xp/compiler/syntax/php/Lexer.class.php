<?php namespace xp\compiler\syntax\php;

use text\StringTokenizer;
use text\StreamTokenizer;
use xp\compiler\emit\Strings;
use lang\IllegalStateException;
use lang\XPClass;

/**
 * Lexer for PHP
 *
 * @see      xp://text.parser.generic.AbstractLexer
 * @purpose  Lexer
 */
class Lexer extends \text\parser\generic\AbstractLexer {
  protected static
    $keywords  = array(
      'public'        => Parser::T_PUBLIC,
      'private'       => Parser::T_PRIVATE,
      'protected'     => Parser::T_PROTECTED,
      'static'        => Parser::T_STATIC,
      'final'         => Parser::T_FINAL,
      'abstract'      => Parser::T_ABSTRACT,
      'const'         => Parser::T_CONST,
      
      'use'           => Parser::T_USE,
      'namespace'     => Parser::T_NAMESPACE,
      'class'         => Parser::T_CLASS,
      'interface'     => Parser::T_INTERFACE,
      'extends'       => Parser::T_EXTENDS,
      'implements'    => Parser::T_IMPLEMENTS,
      'instanceof'    => Parser::T_INSTANCEOF,
      'clone'         => Parser::T_CLONE,     

      'throw'         => Parser::T_THROW,
      'try'           => Parser::T_TRY,
      'catch'         => Parser::T_CATCH,
      
      'return'        => Parser::T_RETURN,
      'new'           => Parser::T_NEW,
      'as'            => Parser::T_AS,
      'array'         => Parser::T_ARRAY,
      'function'      => Parser::T_FUNCTION,
      
      'for'           => Parser::T_FOR,
      'foreach'       => Parser::T_FOREACH,
      'in'            => Parser::T_IN,
      'do'            => Parser::T_DO,
      'while'         => Parser::T_WHILE,
      'break'         => Parser::T_BREAK,
      'continue'      => Parser::T_CONTINUE,
      'yield'         => Parser::T_YIELD,

      'if'            => Parser::T_IF,
      'else'          => Parser::T_ELSE,
      'switch'        => Parser::T_SWITCH,
      'case'          => Parser::T_CASE,
      'default'       => Parser::T_DEFAULT,
    );

  protected static
    $lookahead= array(
      '-' => array('-=' => Parser::T_SUB_EQUAL, '--' => Parser::T_DEC, '->' => Parser::T_OBJECT_OPERATOR),
      '>' => array('>=' => Parser::T_GE, '>>' => Parser::T_SHR),
      '<' => array('<=' => Parser::T_SE, '<<' => Parser::T_SHL),
      '.' => array('.=' => Parser::T_CONCAT_EQUAL),
      '+' => array('+=' => Parser::T_ADD_EQUAL, '++' => Parser::T_INC),
      '*' => array('*=' => Parser::T_MUL_EQUAL, '**' => Parser::T_EXP),
      '/' => array('/=' => Parser::T_DIV_EQUAL),
      '%' => array('%=' => Parser::T_MOD_EQUAL),
      '=' => array('==' => Parser::T_EQUALS, '=>' => Parser::T_DOUBLE_ARROW),
      '!' => array('!=' => Parser::T_NOT_EQUALS),
      ':' => array('::' => Parser::T_DOUBLE_COLON),
      '|' => array('||' => Parser::T_BOOLEAN_OR, '|=' => Parser::T_OR_EQUAL),
      '&' => array('&&' => Parser::T_BOOLEAN_AND, '&=' => Parser::T_AND_EQUAL),
      '^' => array('^=' => Parser::T_XOR_EQUAL),
      '?' => array('?>' => -1)
    );

  const 
    DELIMITERS = " |&?!.:;,@%~=<>(){}[]#+-*/\"\\'\r\n\t";
        
  public
    $fileName  = null;

  protected
    $comment   = null,
    $tokenizer = null,
    $forward   = array();

  /**
   * Constructor
   *
   * @param   var input either a string or an InputStream
   * @param   string source
   */
  public function __construct($input, $source) {
    if ($input instanceof \io\streams\InputStream) {
      $this->tokenizer= new StreamTokenizer($input, self::DELIMITERS, true);
    } else {
      $this->tokenizer= new StringTokenizer($input, self::DELIMITERS, true);
    }
    $this->fileName= $source;
    $first= $this->tokenizer->nextToken(" \r\n\t");
    if ('<?php' !== $first) {
      throw new IllegalStateException('First token must be "<?php", have "'.$first.'"');
    }
    $this->position= $this->forward= array(1, strlen($first));   // Y, X
  }

  /**
   * Create a new node 
   *
   * @param   xp.compiler.ast.Node
   * @param   bool comment default false whether to pass comment
   * @return  xp.compiler.ast.Node
   */
  public function create($n, $comment= false) {
    $n->position= $this->position;
    if ($comment && $this->comment) {
      $n->comment= $this->comment;
      $this->comment= null;
    }
    return $n;
  }

  /**
   * Get next token and recalculate position
   *
   * @param   string delim default self::DELIMITERS
   * @return  string token
   */
  protected function nextToken($delim= self::DELIMITERS) {
    $t= $this->tokenizer->nextToken($delim);
    $l= substr_count($t, "\n");
    if ($l > 0) {
      $this->forward[0]+= $l;
      $this->forward[1]= strlen($t) - strrpos($t, "\n");
    } else {
      $this->forward[1]+= strlen($t);
    }
    return $t;
  }
  
  /**
   * Push back token and recalculate position
   *
   * @param   string token
   */
  protected function pushBack($t) {
    $l= substr_count($t, "\n");
    if ($l > 0) {
      $this->forward[0]-= $l;
      $this->forward[1]= strlen($t) - strrpos($t, "\n");
    } else {
      $this->forward[1]-= strlen($t);
    }
    $this->tokenizer->pushBack($t);
  }
  
  /**
   * Throws an error, appending the starting position to the message
   *
   * @param   string class
   * @param   string message
   * @throws  lang.Throwable
   */
  protected function raise($class, $message) {
    throw XPClass::forName($class)->newInstance($message.' starting at line '.$this->position[0].', offset '.$this->position[1]);
  }

  /**
   * Advance this 
   *
   * @return  bool
   */
  public function advance() {
    while ($hasMore= $this->tokenizer->hasMoreTokens()) {
      $this->position= $this->forward;
      $token= $this->nextToken();
      if (false !== strpos(" \n\r\t", $token)) continue;    // Check for whitespace-only

      $length= strlen($token);
      if ("'" === $token{0} || '"' === $token{0}) {
        $this->token= Parser::T_STRING;
        $this->value= '';
        do {
          if ($token{0} === ($t= $this->nextToken($token{0}))) {
            // Empty string, e.g. "" or ''
            break;
          }
          $this->value.= $t;
          $l= strlen($this->value);
          if ($l > 0 && '\\' === $this->value{$l- 1} && !($l > 1 && '\\' === $this->value{$l- 2})) {
            $this->value= substr($this->value, 0, -1).$this->nextToken($token{0});
            continue;
          } 
          if ($token{0} !== $this->nextToken($token{0})) {
            $this->raise('lang.IllegalStateException', 'Unterminated string literal');
          }
          break;
        } while ($hasMore= $this->tokenizer->hasMoreTokens());
        if ('"' === $token{0}) {
          try {
            $this->value= Strings::expandEscapesIn($this->value);
          } catch (\lang\FormatException $e) {
            $this->raise('lang.FormatException', $e->getMessage());
          }
        } else {
          $this->value= str_replace('\\\\', '\\', $this->value);
        }
      } else if ('$' === $token{0}) {
        $this->token= Parser::T_VARIABLE;
        $this->value= substr($token, 1);
      } else if (isset(self::$keywords[$token])) {
        $this->token= self::$keywords[$token];
        $this->value= $token;
      } else if ('/' === $token{0}) {
        $ahead= $this->nextToken();
        if ('/' === $ahead) {           // Single-line comment
          $this->nextToken("\n");
          continue;
        } else if ('*' === $ahead) {    // Multi-line comment
          $comment= '';
          do { 
            $t= $this->nextToken('/'); 
            $comment.= $t;
          } while ('*' !== $t{strlen($t)- 1});
          
          // Copy api doc comments
          if ($comment && '*' === $comment{0}) {
            $this->comment= $comment;
          }
          $this->nextToken('/');
          continue;
        } else if ('=' === $ahead) {
          $this->token= Parser::T_DIV_EQUAL;
          $this->value= '/=';
        } else {
          $this->token= ord($token);
          $this->value= $token;
          $this->pushBack($ahead);
        }
      } else if (isset(self::$lookahead[$token])) {
        $ahead= $this->nextToken();
        $combined= $token.$ahead;
        if (isset(self::$lookahead[$token][$combined])) {
          $this->token= self::$lookahead[$token][$combined];
          $this->value= $combined;
        } else {
          $this->token= ord($token);
          $this->value= $token;
          $this->pushBack($ahead);
        }
      } else if (false !== strpos(self::DELIMITERS, $token) && 1 == strlen($token)) {
        $this->token= ord($token);
        $this->value= $token;
      } else if (0 === strcspn($token, '0123456789')) {     // Numbers, starting with 0..9
        $ahead= $this->nextToken();
        if ('.' === $ahead{0}) {                            // Decimal numbers, next token starts with "."
          $this->token= Parser::T_DECIMAL;
          $decimal= $this->nextToken();
          $length= strlen($decimal);
          $this->value= $token.$ahead.$decimal;
          if ($length !== strcspn($decimal, 'eE')) {
            $ahead= $this->nextToken();
            if ('+' === $ahead{0} || '-' === $ahead{0}) {
              $this->value.= $ahead.$this->nextToken();
              $format= '%d.%d%1[eE]'.$ahead.'%d';
            } else {
              $format= '%d.%d%1[eE]%d';
              $this->pushBack($ahead);
            }
            if (sscanf($this->value, $format, $number, $fraction, $_, $exponent) < 4) {
              $this->raise('lang.FormatException', 'Illegal decimal number <'.$this->value.'>');
            }
          } else {
            if ($length !== strspn($decimal, '0123456789')) {
              $this->raise('lang.FormatException', 'Illegal decimal number <'.$token.$ahead.$decimal.'>');
            }
          }
        } else {                                            // Integers, no "."
          $p= true;
          if (1 === $length) {
            $this->token= Parser::T_NUMBER;
            $this->value= $token;
          } else if ('0' === $token[0] && ('x' === $token[1] || 'X' === $token[1])) {
            if ($length !== strspn($token, '0123456789ABCDEFXabcdefx')) {
              $this->raise('lang.FormatException', 'Illegal hex number <'.$token.'>');
            }
            $this->token= Parser::T_HEX;
            $this->value= $token;
          } else if ('0' === $token[0]) {
            if ($length !== strspn($token, '01234567')) {
              $this->raise('lang.FormatException', 'Illegal octal number <'.$token.'>');
            }
            $this->token= Parser::T_OCTAL;
            $this->value= $token;
          } else if ($length !== strcspn($token, 'eE')) {
            if ('+' === $ahead{0} || '-' === $ahead{0}) {
              $exponent= $ahead.$this->nextToken();
              $format= '%d%1[eE]'.$ahead.'%d';
              $p= false;
            } else {
              $format= '%d%1[eE]%d';
              $exponent= '';
            }
            $this->token= Parser::T_DECIMAL;
            $this->value= $token.$exponent;
            if (sscanf($this->value, $format, $number, $_, $exponent) < 3) {
              $this->raise('lang.FormatException', 'Illegal decimal number <'.$this->value.'>');
            }
          } else {
            if ($length !== strspn($token, '0123456789')) {
              $this->raise('lang.FormatException', 'Illegal number <'.$token.'>');
            }
            $this->token= Parser::T_NUMBER;
            $this->value= $token;
          }
          $p && $this->pushBack($ahead);
        }
      } else {
        $this->token= Parser::T_WORD;
        $this->value= $token;
      }

      
      break;
    }
    
    // DEBUG fprintf(STDERR, "@ %3d,%3d: %d `%s`\n", $this->position[1], $this->position[0], $this->token, addcslashes($this->value, "\0..\17"));
    return -1 === $this->token ? false : $hasMore;
  }
}
