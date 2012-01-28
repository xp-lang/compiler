<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.compilation';

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   */
  abstract class net·xp_lang·tests·compilation·AnnotationsTest extends TestCase {
    protected $scope;
    protected $emitter;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->emitter= new xp·compiler·emit·source·Emitter();
      $this->scope= new TaskScope(new CompilationTask(
        new FileSource(new File(__FILE__), Syntax::forName('xp')),
        new NullDiagnosticListener(),
        new FileManager(),
        $this->emitter
      ));
    }

    /**
     * Compile class from source and return compiled type
     *
     * @param   string src
     * @return  xp.compiler.types.Types
     */
    protected function compile($src) {
      $r= $this->emitter->emit(
        Syntax::forName('xp')->parse(new MemoryInputStream(sprintf($src, 'FixtureClassFor'.get_class($this).ucfirst($this->name)))),
        $this->scope
      );
      $r->executeWith(array());
      return XPClass::forName($r->type()->name());
    }
  }
?>
