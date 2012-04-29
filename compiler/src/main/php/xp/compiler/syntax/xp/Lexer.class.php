<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  $package= 'xp.compiler.syntax.xp';

  uses(
    'text.Tokenizer',
    'text.StringTokenizer', 
    'text.StreamTokenizer', 
    'io.streams.InputStream',
    'xp.compiler.syntax.xp.Parser', 
    'xp.compiler.emit.Strings', 
    'text.parser.generic.AbstractLexer'
  );
  
  /**
   * Lexer for XP language
   *
   * @see      xp://text.parser.generic.AbstractLexer
   * @purpose  Lexer
   */
  class xp�compiler�syntax�xp�Lexer extends AbstractLexer {
    protected static
      $keywords  = array(
        'public'        => xp�compiler�syntax�xp�Parser::T_PUBLIC,
        'private'       => xp�compiler�syntax�xp�Parser::T_PRIVATE,
        'protected'     => xp�compiler�syntax�xp�Parser::T_PROTECTED,
        'static'        => xp�compiler�syntax�xp�Parser::T_STATIC,
        'final'         => xp�compiler�syntax�xp�Parser::T_FINAL,
        'abstract'      => xp�compiler�syntax�xp�Parser::T_ABSTRACT,
        'inline'        => xp�compiler�syntax�xp�Parser::T_INLINE,
        'native'        => xp�compiler�syntax�xp�Parser::T_NATIVE,
        'const'         => xp�compiler�syntax�xp�Parser::T_CONST,
        
        'package'       => xp�compiler�syntax�xp�Parser::T_PACKAGE,
        'import'        => xp�compiler�syntax�xp�Parser::T_IMPORT,
        'class'         => xp�compiler�syntax�xp�Parser::T_CLASS,
        'interface'     => xp�compiler�syntax�xp�Parser::T_INTERFACE,
        'enum'          => xp�compiler�syntax�xp�Parser::T_ENUM,
        'extends'       => xp�compiler�syntax�xp�Parser::T_EXTENDS,
        'implements'    => xp�compiler�syntax�xp�Parser::T_IMPLEMENTS,
        'instanceof'    => xp�compiler�syntax�xp�Parser::T_INSTANCEOF,
        'clone'         => xp�compiler�syntax�xp�Parser::T_CLONE,     

        'operator'      => xp�compiler�syntax�xp�Parser::T_OPERATOR,
        'throws'        => xp�compiler�syntax�xp�Parser::T_THROWS,

        'throw'         => xp�compiler�syntax�xp�Parser::T_THROW,
        'try'           => xp�compiler�syntax�xp�Parser::T_TRY,
        'catch'         => xp�compiler�syntax�xp�Parser::T_CATCH,
        'finally'       => xp�compiler�syntax�xp�Parser::T_FINALLY,
        
        'return'        => xp�compiler�syntax�xp�Parser::T_RETURN,
        'new'           => xp�compiler�syntax�xp�Parser::T_NEW,
        'as'            => xp�compiler�syntax�xp�Parser::T_AS,
        'this'          => xp�compiler�syntax�xp�Parser::T_THIS,
        
        'for'           => xp�compiler�syntax�xp�Parser::T_FOR,
        'foreach'       => xp�compiler�syntax�xp�Parser::T_FOREACH,
        'in'            => xp�compiler�syntax�xp�Parser::T_IN,
        'do'            => xp�compiler�syntax�xp�Parser::T_DO,
        'while'         => xp�compiler�syntax�xp�Parser::T_WHILE,
        'break'         => xp�compiler�syntax�xp�Parser::T_BREAK,
        'continue'      => xp�compiler�syntax�xp�Parser::T_CONTINUE,

        'with'          => xp�compiler�syntax�xp�Parser::T_WITH,

        'if'            => xp�compiler�syntax�xp�Parser::T_IF,
        'else'          => xp�compiler�syntax�xp�Parser::T_ELSE,
        'switch'        => xp�compiler�syntax�xp�Parser::T_SWITCH,
        'case'          => xp�compiler�syntax�xp�Parser::T_CASE,
        'default'       => xp�compiler�syntax�xp�Parser::T_DEFAULT,
      );

    protected static
      $lookahead= array(
        '.' => array('..' => xp�compiler�syntax�xp�Parser::T_DOTS),
        '-' => array('-=' => xp�compiler�syntax�xp�Parser::T_SUB_EQUAL, '--' => xp�compiler�syntax�xp�Parser::T_DEC, '->' => xp�compiler�syntax�xp�Parser::T_ARROW),
        '>' => array('>=' => xp�compiler�syntax�xp�Parser::T_GE),
        '<' => array('<=' => xp�compiler�syntax�xp�Parser::T_SE),
        '~' => array('~=' => xp�compiler�syntax�xp�Parser::T_CONCAT_EQUAL),
        '+' => array('+=' => xp�compiler�syntax�xp�Parser::T_ADD_EQUAL, '++' => xp�compiler�syntax�xp�Parser::T_INC),
        '*' => array('*=' => xp�compiler�syntax�xp�Parser::T_MUL_EQUAL),
        '%' => array('%=' => xp�compiler�syntax�xp�Parser::T_MOD_EQUAL),
        '=' => array('==' => xp�compiler�syntax�xp�Parser::T_EQUALS),
        '!' => array('!=' => xp�compiler�syntax�xp�Parser::T_NOT_EQUALS),
        ':' => array('::' => xp�compiler�syntax�xp�Parser::T_DOUBLE_COLON),
        '|' => array('||' => xp�compiler�syntax�xp�Parser::T_BOOLEAN_OR, '|=' => xp�compiler�syntax�xp�Parser::T_OR_EQUAL),
        '&' => array('&&' => xp�compiler�syntax�xp�Parser::T_BOOLEAN_AND, '&=' => xp�compiler�syntax�xp�Parser::T_AND_EQUAL),
        '^' => array('^=' => xp�compiler�syntax�xp�Parser::T_XOR_EQUAL),
      );

    const 
      DELIMITERS = " ^|&?!.:;,@%~=<>(){}[]#+-*/\"'\r\n\t\$`";

    public
      $fileName  = NULL;

    protected
      $comment   = NULL,
      $tokenizer = NULL,
      $forward   = array();

    /**
     * Constructor
     *
     * @param   var input either a string or an InputStream
     * @param   string source
     */
    public function __construct($input, $source) {
      if ($input instanceof InputStream) {
        $this->tokenizer= new StreamTokenizer($input, self::DELIMITERS, TRUE);
      } else {
        $this->tokenizer= new StringTokenizer($input, self::DELIMITERS, TRUE);
      }
      $this->fileName= $source;
      $this->position= $this->forward= array(1, 1);   // Y, X
    }

    /**
     * Create a new node 
     *
     * @param   xp.compiler.ast.Node
     * @param   bool comment default FALSE whether to pass comment
     * @return  xp.compiler.ast.Node
     */
    public function create($n, $comment= FALSE) {
      $n->position= $this->position;
      if ($comment && $this->comment) {
        $n->comment= $this->comment;
        $this->comment= NULL;
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
      raise($class, $message.' starting at line '.$this->position[0].', offset '.$this->position[1]);
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
        if (FALSE !== strpos(" \n\r\t", $token)) continue;    // Skip whitespace

        $length= strlen($token);
        if ("'" === $token{0} || '"' === $token{0}) {
          $this->token= xp�compiler�syntax�xp�Parser::T_STRING;
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
            } catch (FormatException $e) {
              $this->raise('lang.FormatException', $e->getMessage());
            }
          } else {
            $this->value= str_replace('\\\\', '\\', $this->value);
          }
        } else if ('$' === $token{0}) {
          $this->token= xp�compiler�syntax�xp�Parser::T_VARIABLE;
          $this->value= $this->nextToken();
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
            $this->token= xp�compiler�syntax�xp�Parser::T_DIV_EQUAL;
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
        } else if (FALSE !== strpos(self::DELIMITERS, $token) && 1 === $length) {
          $this->token= ord($token);
          $this->value= $token;
        } else if (0 === strcspn($token, '0123456789')) {     // Numbers, starting with 0..9
          $ahead= $this->nextToken();
          if ('.' === $ahead{0}) {                            // Decimal numbers, next token starts with "."
            $this->token= xp�compiler�syntax�xp�Parser::T_DECIMAL;
            $decimal= $this->nextToken();
            $length= strlen($decimal);
            $this->value= $token.$ahead.$decimal;
            if ($length !== strcspn($decimal, 'eE')) {
              $ahead= $this->nextToken();
              if ('+' === $ahead{0} || '-' === $ahead{0}) {
                $this->value.= $ahead.$this->nextToken();
                $format= '%d.%d%*1[eE]'.$ahead.'%*d';
              } else {
                $format= '%d.%d%*1[eE]%*d';
                $this->pushBack($ahead);
              }
              if (4 !== sscanf($this->value, $format, $n, $f)) {
                $this->raise('lang.FormatException', 'Illegal decimal number <'.$this->value.'>');
              }
            } else {
              if ($length !== strspn($decimal, '0123456789')) {
                $this->raise('lang.FormatException', 'Illegal decimal number <'.$token.$ahead.$decimal.'>');
              }
            }
          } else {                                            // Integers, no "."
            $p= TRUE;
            if (1 === $length) {
              $this->token= xp�compiler�syntax�xp�Parser::T_NUMBER;
              $this->value= $token;
            } else if ('0' === $token[0] && ('x' === $token[1] || 'X' === $token[1])) {
              if ($length !== strspn($token, '0123456789ABCDEFXabcdefx')) {
                $this->raise('lang.FormatException', 'Illegal hex number <'.$token.'>');
              }
              $this->token= xp�compiler�syntax�xp�Parser::T_HEX;
              $this->value= $token;
            } else if ('0' === $token[0]) {
              if ($length !== strspn($token, '01234567')) {
                $this->raise('lang.FormatException', 'Illegal octal number <'.$token.'>');
              }
              $this->token= xp�compiler�syntax�xp�Parser::T_OCTAL;
              $this->value= $token;
            } else if ($length !== strcspn($token, 'eE')) {
              if ('+' === $ahead{0} || '-' === $ahead{0}) {
                $exponent= $ahead.$this->nextToken();
                $format= '%d%*1[eE]'.$ahead.'%*d';
                $p= FALSE;
              } else {
                $format= '%d%*1[eE]%*d';
                $exponent= '';
              }
              $this->token= xp�compiler�syntax�xp�Parser::T_DECIMAL;
              $this->value= $token.$exponent;
              if (3 !== sscanf($this->value, $format, $n)) {
                $this->raise('lang.FormatException', 'Illegal decimal number <'.$this->value.'>');
              }
            } else {
              if ($length !== strspn($token, '0123456789')) {
                $this->raise('lang.FormatException', 'Illegal number <'.$token.'>');
              }
              $this->token= xp�compiler�syntax�xp�Parser::T_NUMBER;
              $this->value= $token;
            }
            $p && $this->pushBack($ahead);
          }
        } else {
          $this->token= xp�compiler�syntax�xp�Parser::T_WORD;
          $this->value= $token;
        }
        
        break;
      }
      
      // DEBUG fprintf(STDERR, "@ %3d,%3d: %d `%s`\n", $this->position[1], $this->position[0], $this->token, addcslashes($this->value, "\0..\17"));
      return -1 === $this->token ? FALSE : $hasMore;
    }
  }
?>
