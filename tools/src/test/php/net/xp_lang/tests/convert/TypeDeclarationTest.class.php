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
        'package class Name { }',
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

    /**
     * Test
     *
     */
    #[@test]
    public function genericClass() {
      $this->assertConversion(
        'public class ListOf<T> { }',
        "#[@generic(self= 'T')]\nclass ListOf { }",
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function genericImplements() {
      $this->name('IList', 'util.collections.IList');
      $this->assertConversion(
        'public class ListOf<T> implements util.collections.IList<T> { }',
        "#[@generic(self= 'T', implements= array('T'))]\nclass ListOf implements IList { }",
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function genericParent() {
      $this->name('ListOf', 'com.example.ListOf');
      $this->assertConversion(
        'public class SynchronizedListOf<T> extends com.example.ListOf<T> { }',
        "#[@generic(self= 'T', parent= 'T')]\nclass SynchronizedListOf extends ListOf { }",
        SourceConverter::ST_NAMESPACE
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function genericClassWithTwoArguments() {
      $this->assertConversion(
        'public class MapOf<K, V> { }',
        "#[@generic(self= 'K, V')]\nclass MapOf { }",
        SourceConverter::ST_NAMESPACE
      );
    }
  }
?>
