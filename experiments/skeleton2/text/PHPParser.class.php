<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.File',
    'io.FileUtil',
    'util.log.Traceable'
  );

  // Modes of operation
  define('PHPPARSER_MODE_UNDEF',            0x0000);
  define('PHPPARSER_MODE_GET_FUNC_NAME',    0x0001);
  define('PHPPARSER_MODE_GET_CLASS_NAME',   0x0002);
  define('PHPPARSER_MODE_GET_USES',         0x0003);
  define('PHPPARSER_MODE_GET_REQUIRE',      0x0004);
  define('PHPPARSER_MODE_GET_SAPIS',        0x0005);

  /**
   * Class to parse PHP files. Parsing tries to extract informations
   * about contained global functions, classes and its functions.
   *
   * @ext      tokenizer
   * @purpose  Parser
   */
  class PHPParser extends Object implements Traceable {
    public
      $functions= array(),
      $classes=   array(),
      $uses=      array(),
      $requires=  array(),
      $sapis=     array(),
      $filename=  '',
      $log=       NULL;
    
    protected
      $_utimeLastChange= 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename The filename to parse
     */
    public function __construct($filename) {
      
      $this->filename= $filename;
    }
    
    /**
     * Sets a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory log
     */
    public function setTrace(LogCategory $log) {
      $this->log= $log;
    }    
    
    /**
     * Returns whether this file needs reparsing
     *
     * @access  public
     * @return  boolean needreparseing
     */
    public function needsReparsing() {
      if (NULL === $this->filename) return FALSE;
      
      if (FALSE !== ($mtime= filemtime ($this->filename)))
        return ($mtime > self::getLastChange());
      
      return TRUE;
    }
    
    /**
     * Get time of last parsing
     *
     * @access  public
     * @return  int utime
     */
    public function getLastChange() {
      return $this->_utimeLastChange;
    }

    /**
     * Set time of last parsing
     *
     * @access  public
     * @param   int lp Unix-timestamp
     */
    public function setLastChange($lp) {
      $this->_utimeLastChange= $lp;
    }
    
    /**
     * Clears for reparsing
     *
     * @access  public
     */
    public function clear() {
      $this->functions= $this->classes= $this->uses= $this->requires= $this->sapis= array();
      self::setLastChange(0);
    }     

    /**
     * Parses the file.
     *
     * @access  public
     * @return  bool success
     * @throws  lang.Exception in case the file could not be read
     */
    public function parse() {
      try {
        $php= FileUtil::getContents (new File ($this->filename));
      } catch (XPException $e) {
        throw  ($e);
      }
      
      $tokens= token_get_all ($php);
      $lineno= 1;

      // Clean up old data
      self::clear();
      
      $currentSpace= array();
      $currentClass= array();

      $mode= PHPPARSER_MODE_UNDEF;
      foreach ($tokens as $token) {
        if (is_string ($token)) {

          // No active 'workspace' => continue
          if (empty($currentSpace)) continue;

          $s= $currentSpace[sizeof($currentSpace)- 1];
          
          switch ($token) {
            case '{':
              $s->braces++;
              break;
              
            case '}':
              $s->braces--;
              break;
            
            default:
              continue (2);
          }
          
          // If $s->braces is 0, pop the item off the stack
          if (0 == $s->braces) {
            $this->log && $this->log->debug ($s->type, $s->name, 'ends at line', $lineno);
            
            unset ($s->braces);
            $s->endsAt= $lineno;
            
            // Remove things from the stack
            if ('class' == $s->type) array_pop ($currentClass);
            array_pop ($currentSpace);
          }
          
          continue;
        }

        list ($tokenId, $data)= $token;
        switch ($mode) {
          case PHPPARSER_MODE_UNDEF:
            switch ($tokenId) {
              case T_CONSTANT_ENCAPSED_STRING:
              case T_CLOSE_TAG:
              case T_OPEN_TAG:
              case T_START_HEREDOC:
              case T_END_HEREDOC:
              case T_WHITESPACE:
              case T_ENCAPSED_AND_WHITESPACE:
              case T_INLINE_HTML:
              case T_ML_COMMENT:
              case T_NUM_STRING:
              case T_STRING:
              case T_COMMENT:
                $lineno+= substr_count ($data, "\n");
                break;

              case T_FUNCTION:
                $mode= PHPPARSER_MODE_GET_FUNC_NAME;
                break;

              case T_CLASS:
                $mode= PHPPARSER_MODE_GET_CLASS_NAME;
                break;
              
              default:
                break;
            }
            break;

          case PHPPARSER_MODE_GET_FUNC_NAME:
            switch ($tokenId) {
              case T_WHITESPACE:
                break;

              case T_STRING:
                $this->log && $this->log->info ('Function', $data, 'at line', $lineno);
                
                // Add new function, if currentClass exists add to that, otherwise to file
                $f= new StdClass();
                $f->type= 'function';
                $f->name= $data;
                $f->line= $lineno;
                $f->braces= 0;
                
                if (!empty($currentClass)) {
                  $c= $currentClass[sizeof($currentClass)- 1];
                  $c->functions[]= $f;
                } else {
                  $this->functions[]= $f;
                }
                
                $currentSpace[]= $f;
                
                $mode= PHPPARSER_MODE_UNDEF;
                break;
            }
            break;

          case PHPPARSER_MODE_GET_CLASS_NAME:
            switch ($tokenId) {
              case T_WHITESPACE:
                break;

              case T_STRING:
                $this->log && $this->log->info ('Class', $data, 'at line', $lineno);
                
                // Add new class object, and remember this as the active
                $c= new StdClass();
                $c->type= 'class';
                $c->name= $data;
                $c->line= $lineno;
                $c->braces= 0;
                $c->functions= array();
                
                $this->classes[]= $c;
                $currentClass[]= $c;
                $currentSpace[]= $c;
                
                $mode= PHPPARSER_MODE_UNDEF;
                break;
            }
            break;
        }
      }
      
      // 2nd pass: try to find uses(), require() and include()
      $brace= 0; $mode= PHPPARSER_MODE_UNDEF;
      foreach ($tokens as $offset => $token) {
        if (is_string($token)) {
          switch ($token) {
            case '(':
              $brace++;
              break;
            case ')':
              $brace--;
              break;
            
            default:
              break;
          }

          if ($brace == 0) $mode= PHPPARSER_MODE_UNDEF;
          continue;
        }
        
        list ($tokenId, $data)= $token;
        switch ($tokenId) {
          case T_STRING:
            switch ($data) {
              case 'uses':      // uses() 
                $mode= PHPPARSER_MODE_GET_USES; 
                break;

              case 'sapi':      // xp::sapi()
                if ('xp::' != $tokens[$offset- 2][1].$tokens[$offset- 1][1]) break;
                $mode= PHPPARSER_MODE_GET_SAPIS; 
                break;

              default:          // Ignore
            }
            break;
          
          case T_INCLUDE:
          case T_INCLUDE_ONCE:
          case T_REQUIRE:
          case T_REQUIRE_ONCE:
            $mode= PHPPARSER_MODE_GET_REQUIRE;
            break;
        }
        
        switch ($mode) {
          case PHPPARSER_MODE_GET_USES:
            if (T_CONSTANT_ENCAPSED_STRING == $tokenId) 
              $this->uses[]= substr ($data, 1, strlen ($data)-2);
            break;
          
          case PHPPARSER_MODE_GET_REQUIRE:
            if (T_CONSTANT_ENCAPSED_STRING == $tokenId) 
              $this->requires[]= substr ($data, 1, strlen ($data)-2);
            break;
          
          case PHPPARSER_MODE_GET_SAPIS:
            if (T_CONSTANT_ENCAPSED_STRING == $tokenId) 
              $this->sapis[]= substr ($data, 1, strlen ($data)-2);
            break;
          
          default:
            break;
        }
      }
      
      self::setLastChange(time());
      return TRUE;
    }
  }
?>
