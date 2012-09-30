<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests finally
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class FinallyConversionTest extends AbstractConversionTest {

    /**
     * Test NULL
     *
     */
    #[@test]
    public function syntaxRewritten() {
      $this->name('Throwable', 'lang.Throwable');
      $this->assertConversion(
        'try { /* ... */ } catch (lang.Throwable $e) { } finally { /* ... */ if ($e) throw($e); }',
        'try { /* ... */ } catch (Throwable $e) { } finally(); { /* ... */ if ($e) throw($e); }',
        SourceConverter::ST_FUNC_BODY
      );
    }

  }
?>
