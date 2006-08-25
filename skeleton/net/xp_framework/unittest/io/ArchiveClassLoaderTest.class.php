<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'io.cca.ArchiveClassLoader',
    'io.cca.Archive',
    'io.Stream'
  );

  /**
   * TestCase
   *
   * @see      xp://io.cca.ArchiveClassLoader
   * @purpose  purpose
   */
  class ArchiveClassLoaderTest extends TestCase {
    var
      $classloader     = NULL,
      $classname       = '',
      $interfacename   = '';

    /**
     * Returns class bytes as a stream
     *
     * @access  protected
     * @param   string bytes
     * @return  &io.Stream
     */
    function &classStream($bytes) {
      $cstr= &new Stream();
      $cstr->open(STREAM_MODE_WRITE);
      $cstr->write('<?php '.$bytes.' ?>');
      $cstr->close();
      
      return $cstr;
    }
    
    /**
     * Creates a unique class name for the running test case
     *
     * @access  protected
     * @param   string prefix default ''
     * @return  string
     * @throws  lang.IllegalStateException in case the generated class name already exists!
     */
    function testClassName($prefix= '') {
      $classname= $prefix.'ClassUsedForArchiveClassLoader'.ucfirst($this->name).'Test';
      if (class_exists($classname)) {
        return throw(new IllegalStateException('Class '.$this->classname.' may not exist!'));
      }
      return $classname;
    }

    /**
     * Sets up test case
     *
     * @access  public
     */
    function setUp() {
      try(); {
        $this->classname= $this->testClassName();
        $this->interfacename= $this->testClassName('I');
      } if (catch('IllegalStateException', $e)) {
        return throw(new PrerequisitesNotMetError($e->getMessage()));
      }

      // Create an archive
      $archive= &new Archive(new Stream());
      $archive->open(ARCHIVE_CREATE);
      $archive->add(
        $this->classStream(
          'class '.$this->classname.' extends Object { 
          
          } implements(__FILE__, "'.$this->interfacename.'");
        '), 
        $this->classname
      );
      $archive->add(
        $this->classStream(
          'class '.$this->interfacename.' extends Interface { 
        
          }
        '), 
        $this->interfacename
      );
      $archive->create();
      
      // Setup classloader
      $this->classloader= &new ArchiveClassLoader($archive);
    }

    /**
     * Test loadClass() method
     *
     * @access  public
     */
    #[@test]
    function loadClass() {
      $class= &$this->classloader->loadClass($this->classname);
      $this->assertEquals($class->getName(), $this->classname);
    }
  }
?>
