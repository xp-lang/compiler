<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.convert';

  uses(
    'unittest.TestCase',
    'net.xp_lang.convert.SourceConverter'
  );

  /**
   * TestCase
   *
   * @see      xp://cmd.convert.SourceConverter
   */
  class net·xp_lang·tests·convert·IntegrationTest extends TestCase {
    protected $fixture;
    protected $classloader;

    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new SourceConverter();
      $this->classloader= $this->getClass()->getPackage()->getPackage('res');
    }
    
    /**
     * Calculates the difference between two strings, ignoring trailing 
     * whitespace
     *
     * @param   string a
     * @param   string b
     * @return  string
     */
    protected function diff($a, $b) {
      $al= explode("\n", $a);
      $bl= explode("\n", $b);
      $r= '';
      for ($i= 0, $s= max(sizeof($al), sizeof($bl)); $i < $s; $i++) {
        if (rtrim(@$al[$i]) === rtrim(@$bl[$i])) continue;
        isset($al[$i]) && $r.= '- '.$al[$i]."\n";
        isset($bl[$i]) && $r.= '+ '.$bl[$i]."\n";
      }
      return $r;
    }
    
    /**
     * Assertion helper
     *
     * @param   string qname
     * @throws  unittest.AssertionFailedError
     */
    protected function assertConversion($qname) {
      $localname= substr($qname, strrpos($qname, '.')+ 1);
      if ('' !== ($diff= $this->diff(
        $this->classloader->getResource($localname.'.xp'),
        $this->fixture->convert($qname, token_get_all($this->classloader->getResource($localname.'.php')))
      ))) {
        $this->fail('not equal', "\n".$diff, '(->'.$localname.'.xp)');
      }
    }
  
    /**
     * Test an empty class
     *
     */
    #[@test]
    public function throwableClass() {
      $this->assertConversion('lang.Throwable');
    }

    /**
     * Test an interface
     *
     */
    #[@test]
    public function runnableInterface() {
      $this->assertConversion('lang.Runnable');
    }

    /**
     * Test an enum
     *
     */
    #[@test, @ignore('Member rewriting not yet implemented')]
    public function coinEnum() {
      $this->assertConversion('demo.Coin');
    }

    /**
     * Test constructor rewriting
     *
     */
    #[@test]
    public function constructorRewriting() {
      $this->assertConversion('lang.String');
    }

    /**
     * Test fully qualifying of class names
     *
     */
    #[@test]
    public function notUsedClassesFullyQualified() {
      $this->fixture->nameMap['Object']= 'lang.Object';
      $this->fixture->nameMap['Date']= 'util.Date';
      $this->fixture->nameMap['Help']= 'demo.Help';
      $this->assertConversion('demo.Application');
    }
  }
?>
