<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests named arguments
   *
   * @see   https://github.com/xp-framework/rfc/issues/251
   */
  class net·xp_lang·tests·execution·source·NamedArgsTest extends ExecutionTest {

    /**
     * Test passing arguments
     *
     * @param   string arguments
     * @return  [:var]
     */
    protected function pass($arguments) {
      $fixture= $this->define('class', $this->name, NULL, '{
        public [:var] fixture(string $url= null, [:string] $params= [:], [:string] $headers= [:]) {
          return [ url: $url, params: $params, headers: $headers];
        }
        
        public [:var] run() {
          return $this.fixture('.$arguments.');
        }
      }');
      return $fixture->newInstance()->run();
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function pass_all_without_names() {
      $this->assertEquals(
        array('url' => '/', 'params' => array('a' => 'b'), 'headers' => array('X' => 'Y')),
        $this->pass('"/", [ a: "b" ], [ X: "Y"]')
      );
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function pass_first_without_name() {
      $this->assertEquals(
        array('url' => '/', 'params' => array(), 'headers' => array()),
        $this->pass('"/"')
      );
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function pass_all_with_names() {
      $this->assertEquals(
        array('url' => '/', 'params' => array('a' => 'b'), 'headers' => array('X' => 'Y')),
        $this->pass('url: "/", params: [ a: "b" ], headers: [ X: "Y"]')
      );
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function pass_all_with_names_divergent_order() {
      $this->assertEquals(
        array('url' => '/', 'params' => array('a' => 'b'), 'headers' => array('X' => 'Y')),
        $this->pass('url: "/", headers: [ X: "Y"], params: [ a: "b" ]')
      );
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function pass_first_with_name() {
      $this->assertEquals(
        array('url' => '/', 'params' => array(), 'headers' => array()),
        $this->pass('url: "/"')
      );
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function pass_first_two_with_names() {
      $this->assertEquals(
        array('url' => '/', 'params' => array('a' => 'b'), 'headers' => array()),
        $this->pass('url: "/", params: [ a: "b" ]')
      );
    }
  }
?>
