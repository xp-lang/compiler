<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest',
    'scriptlet.xml.workflow.casters.ToDate'
  );
  
  /**
   * Test the ToDate caster
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
   * @see       scriptlet.xml.workflow.casters.ToDate
   * @purpose   ToDate test
   */
  class ToDateTest extends AbstractCasterTest {

    /**
     * Return the caster
     *
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    protected function caster() {
      return new ToDate();
    }

    /**
     * Test european date format (DD.MM.YYYY)
     *
     */
    #[@test]
    public function europeanDateFormat() {
      $this->assertEquals(new Date('1977-12-14'), $this->castValue('14.12.1977'));
    }

    /**
     * Test US date format (YYYY-MM-DD)
     *
     */
    #[@test]
    public function usDateFormat() {
      $this->assertEquals(new Date('1977-12-14'), $this->castValue('1977-12-14'));
    }

    /**
     * Test empty input
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function emptyInput() {
      $this->castValue('');
    }
  }
?>
