<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'io.Stream'
  );
  
  /**
   * CSVParser provides comfortable way to parse csv (comma separated
   * value) - files. This class proposes "|" as default delimiter, tough.
   *
   * @purpose Interface for parsing CSV-files
   * @see http://www.creativyst.com/Doc/Articles/CSV/CSV01.htm
   */
  class CSVParser extends Object {
    var
      $stream;
    
    var
      $hasHeader,
      $colDelim= "|",
      $escape= '"';
    
    var
      $colName;
      
    var 
      $buffer;
    
    /**
     * Creates a CSVParser object
     *
     * @access public
     * @param int mode header or headerless mode
     */    
    function __construct() {
      $this->buffer= '';
      $this->colName= NULL;
    }

    /**
     * Tokenizes a string according to our needs.
     *
     * @access private
     * @param string &string string to take token of
     * @param char delim delimiter
     * @return string token
     */
    function _strtok(&$string, $delim) {
      /* Note: don't use builtin strtok, because it does ignore an
       * empty field (two delimiters in a row). We need this information.
       */
      if (empty ($string))
        return false;
      if (false === ($tpos= strpos ($string, $delim))) {
        $token= $string;
        $string= '';
        return $token;
      }
      
      $token= substr ($string, 0, $tpos);
      $string= substr ($string, strlen ($token)+1);
      return $token;
    }
    
    /**
     * Sets the input stream. This stream must support
     * isOpen(), open(), eof(), readLine().
     *
     * @access public
     * @param Stream stream
     * @return  
     */    
    function setInputStream(&$stream) {
      try(); {
        if (!$stream->isOpen ()) $stream->open ();
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      $this->stream= &$stream;
    }
    
    /**
     * Sets the new delimiter for columns. Once CSVs had comma "," as
     * delimiters, today this varies. The pipe "|" is often used
     * as delimiter. It only makes sense to call this before any
     * line was read.
     *
     * @access public
     * @param char delimiter delimiter to set
     */
    function setColDelimiter($delim) {
      $this->colDelim= $delim{0};
    }

    /**
     * Returns whether the header record has already been read.
     * There is no information in the CSV itself that states whether
     * an header record is available, so this has to be decided by
     * the calling program (or user).
     *
     * @access public
     * @return bool hasHeader true, if header is available
     */
    function hasHeader() {
      return is_array ($this->colName) && count ($this->colName);
    }
    
    /**
     * Reads as many lines as necessary from the stream until there is 
     * exactly one record in the buffer.
     * This function affects the member buffer.
     *
     * @access private
     * @return string buffer
     */    
    function &_getNextRecord() {
      try(); {
        if ($this->stream->eof())
          return false;

        $row= $this->stream->readLine();
        while (0 !== substr_count ($row, $this->escape) % 2)
          $row.= "\n".$this->stream->readLine();
      
        $this->buffer= $row;
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return $this->buffer;
    }

    /**
     * Parse the next cell out of the buffer. If buffer is empty,
     * this returns false. This function takes care of
     * quotedness of the data, and de-escapes any escaped chars.
     * It also removes the parsed cell from the internal buffer.
     *
     * @access private
     * @return string buffer
     */
    function _parseColumn() {
      if (empty ($this->buffer))
        return false;

      $tok= $this->_strtok ($this->buffer, $this->colDelim);
      /* 
       * Trick: when there is an odd number of escape characters
       * you know that this found delimiter is part of a string inside
       * the payload. Search for the next, until you have an even number 
       * of escapers (then all quoted parts are closed).
       */
      while (0 !== substr_count ($tok, $this->escape) % 2) {
        $add= $this->colDelim.$this->_strtok ($this->buffer, $this->colDelim);
        $tok.= $add;
      }
      // $this->buffer= substr ($this->buffer, strlen ($tok)+1);
      
      /* 
       * Single escape characters become nothing, double escape
       * characters become the escape character itself.
       */
      $tok= trim ($tok);
      $i= 0; $j= 0; $res= '';
      while (false !== ($i= strpos ($tok, $this->escape, $j))) {
        if (strlen ($tok) > $i+1 && $tok{$i+1} == $this->escape) $i++;
        $res.= substr ($tok, $j, $i-$j);
        $j= $i+1;
      }
      
      if (empty ($res))
        return $tok;
      
      return $res; 
    }
    
    /**
     * Read the record and save the result as the header record.
     *
     * @access public
     */    
    function getHeaderRecord() {
      $this->colName= (array)$this->getNextRecord();
    }
    
    /**
     * Manually set the header information to be able to supply
     * additional information and get nicer output (non-enumerated)
     *
     * @access public
     * @param array headers
     */    
    function setHeaderRecord($headers) {
      $this->colName= $headers;
    }
    
    /**
     * Read the next record from the stream. This returns a 
     * StdClass object with the members named as the header
     * record supposes. When no header was available, the
     * fields are enumerated.
     *
     * @access public
     * @return StdClass data
     * @throws IOException if stream operation failed
     */    
    function getNextRecord() {
      try(); {
        $this->_getNextRecord();
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }

      if (empty ($this->buffer))
        return false;
        
      $data= array(); $idx= 0;
      while (false !== ($cell= $this->_parseColumn())) {
        if ($this->hasHeader())
          $data[$this->colName[$idx]]= $cell;
        else 
          $data[]= $cell;
        
        $idx++;
      }

      return (object)$data;
    }
  }
  
?>
