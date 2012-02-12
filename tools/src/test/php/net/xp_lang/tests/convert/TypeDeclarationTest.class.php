<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.convert';

  uses('net.xp_lang.tests.convert.AbstractConversionTest');

  /**
   * Tests type declarations
   *
   * @see      xp://tests.convert.AbstractConversionTest
   */
  class net·xp_lang·tests·convert·TypeDeclarationTest extends AbstractConversionTest {

    /**
     * Test
     *
     */
    #[@test]
    public function publicModifierAddedToClass() {
      $this->assertConversion(
        'public class Object { }',
        'class Object { }',
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function publicModifierAddedToInterface() {
      $this->assertConversion(
        'public interface Runnable { }',
        'interface Runnable { }',
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function fullyQualified() {
      $this->assertConversion(
        'public class Name { }',
        'class fully·qualified·Name { }',
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function extendsObjectRemoved() {
      $this->name('Object', 'lang.Object');
      $this->assertConversion(
        'public class String { }',
        'class String extends Object { }',
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function extendsThrowableNotRemoved() {
      $this->name('Throwable', 'lang.Throwable');
      $this->assertConversion(
        'public class Error extends lang.Throwable { }',
        'class Error extends Throwable { }',
        SourceConverter::ST_NAMESPACE
      );
    }
  }
?>
