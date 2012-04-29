<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * String emittance utilities
   *
   * String escapes
   * --------------
   * <pre>
   *   \\       A literal \
   *   \b       Backspace
   *   \t       Tab
   *   \n       Newline
   *   \f       Formfeed
   *   \r       Carriage retrn
   *   \X{1,3}  Octal escape (X is an octal number), \0 .. \377
   * </pre>
   *
   * @see http://java.sun.com/docs/books/jls/third_edition/html/lexical.html#3.10.6
   */
  class Strings extends Object {
    
    /**
     * Expand escape sequences inside a given string and return it
     *
     * @param   string in
     * @return  string out
     * @throws  lang.FormatException in case an illegal escape sequence is encountered
     */
    public static function expandEscapesIn($in) {
      if (0 === ($s= strlen($in))) return $in;

      $offset= 0;
      $out= '';
      while (FALSE !== ($p= strpos($in, '\\', $offset))) {
        $out.= substr($in, $offset, $p- $offset);
        $offset= $p+ 1;
        if ($offset >= $s || '\\' == $in{$offset}) {
          $out.= '\\';
        } else if ('r' === $in{$offset}) {
          $out.= "\015";
        } else if ('b' === $in{$offset}) {
          $out.= "\010";
        } else if ('n' === $in{$offset}) {
          $out.= "\012";
        } else if ('t' === $in{$offset}) {
          $out.= "\011";
        } else if ('f' === $in{$offset}) {
          $out.= "\014";
        } else if ('x' === $in{$offset} && ($p= strspn($in, '0123456789ABCDEFabcdef', $offset+ 1))) {
          if (($n= hexdec(substr($in, $offset+ 1, $p))) > 0xFF) {
            throw new FormatException('Hex number out of range (\x00 .. \xFF) in '.$in);
          }
          $out.= chr($n);
          $offset+= $p;
        } else if ($p= strspn($in, '01234567', $offset)) {
          if (($n= octdec(substr($in, $offset, $p))) > 0xFF) {
            throw new FormatException('Octal number out of range (\0 .. \377) in '.$in);
          }
          $out.= chr($n);
          $offset+= $p- 1;
        } else {
          throw new FormatException('Illegal escape sequence \\'.$in{$offset}.' in '.$in);
        }
        if (++$offset > $s) break;
      }
      return $out.substr($in, $offset);
    }
    
  }
?>
