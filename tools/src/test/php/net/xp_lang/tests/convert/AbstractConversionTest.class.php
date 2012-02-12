<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_lang.convert.SourceConverter',
    'lang.types.String'
  );

  /**
   * Abstract base class for conversion tests
   *
   * @see      xp://cmd.SourceConverter
   */
  abstract class AbstractConversionTest extends TestCase {
    protected $fixture;

    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new SourceConverter();
    }
    
    /**
     * Map a given name
     *
     * @param   string local
     * @param   string qualified
     */
    public function name($local, $qualified) {
      $this->fixture->nameMap[$local]= $qualified;
    }
    
    /**
     * Assertion helper
     *
     * @param   string src
     * @param   string src
     * @param   string state
     * @param   string qname defaults to unittest name
     * @throws  unittest.AssertionFailedError
     */
    protected function assertConversion($expect, $src, $state, $qname= NULL) {
      $this->assertEquals($expect, $this->fixture->convert(
        $qname ? $qname : $this->name, 
        array_slice(token_get_all('<?php '.$src.'?>'), 1, -1), 
        $state
      ));
    }
  }
?>
