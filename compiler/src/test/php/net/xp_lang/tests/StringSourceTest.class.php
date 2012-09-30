<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.io.StringSource',
    'io.streams.Streams'
  );

  /**
   * TestCase
   *
   * @see   xp://xp.compiler.io.StringSource
   */
  class StringSourceTest extends TestCase {
    protected static $syntax;
  
    /**
     * Use XP language
     *
     */
    #[@beforeClass]
    public static function useXpSyntax() {
      self::$syntax= Syntax::forName('xp');
    }
    
    /**
     * Creates a new fixture
     *
     * @param   string source
     * @return  xp.compiler.io.StringSource
     */
    protected function newInstance($source= NULL) {
      return new StringSource($source, self::$syntax, $this->name);
    }
  
    /**
     * Test getInputStream()
     *
     */
    #[@test]
    public function getInputStream() {
      $source= 'Console::writeLine("Hello");';
      $this->assertEquals($source, Streams::readAll($this->newInstance($source)->getInputStream()));
    }

    /**
     * Test getSyntax()
     *
     */
    #[@test]
    public function getSyntax() {
      $this->assertEquals(self::$syntax, $this->newInstance()->getSyntax());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function getURI() {
      $this->assertEquals($this->name, $this->newInstance()->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function getURIWithNameOmitted() {
      $this->assertEquals(
        'Compiled source #0', 
        create(new StringSource(NULL, self::$syntax))->getURI()
      );
    }
  }
?>
