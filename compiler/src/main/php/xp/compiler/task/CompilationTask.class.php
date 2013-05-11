<?php namespace xp\compiler\task;

use xp\compiler\types\TypeReference;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use xp\compiler\CompilationException;
use xp\compiler\Syntax;
use lang\ClassLoader;
use lang\ElementNotFoundException;

/**
 * Represents a compilation task
 *
 * @see   xp://xp.compiler.Compiler#compile
 * @test  xp://net.xp_lang.tests.integration.CircularDependencyTest
 */
class CompilationTask extends \lang\Object {
  protected
    $source     = null,
    $manager    = null,
    $listener   = null,
    $emitter    = null,
    $done       = null;

  /**
   * Constructor
   *
   * @param   xp.compiler.io.Source source
   * @param   xp.compiler.diagnostic.DiagnosticListener listener
   * @param   xp.compiler.io.FileManager manager
   * @param   xp.compiler.emit.Emitter emitter
   * @param   util.collections.HashTable<xp.compiler.io.Source, xp.compiler.types.Types> done
   */
  public function __construct(
    \xp\compiler\io\Source $source, 
    \xp\compiler\diagnostic\DiagnosticListener $listener, 
    \xp\compiler\io\FileManager $manager, 
    \xp\compiler\emit\Emitter $emitter,
    $done= null
  ) {
    $this->source= $source;
    $this->manager= $manager;
    $this->listener= $listener;
    $this->emitter= $emitter;
    $this->done= $done ? $done : create('new util.collections.HashTable<xp.compiler.io.Source, xp.compiler.types.Types>()');
  }

  /**
   * Locate a package
   *
   * @param   string name
   * @return  string qualified
   * @throws  lang.ElementNotFoundException
   */
  public function locatePackage($name) {
    if (ClassLoader::getDefault()->providesPackage($name) || $this->manager->findPackage($name)) {
      return $name;
    }
    throw new ElementNotFoundException('Could not locate package '.$name);
  }
  
  /**
   * Locate a class
   *
   * @param   string[] packages
   * @param   string name
   * @return  string qualified
   * @throws  lang.ElementNotFoundException
   */
  public function locateClass($packages, $local) {
    $cl= ClassLoader::getDefault();
    foreach ($packages as $package) {
      $qualified= $package.'.'.$local;
      if (!$cl->providesClass($qualified) && !$this->manager->findClass($qualified)) continue;
      return $qualified;
    }
    throw new ElementNotFoundException('Could not locate class '.$local.' in '.\xp::stringOf($packages));
  }
  
  /**
   * Returns a subtask (overloaded)
   *
   * @param   var arg either a xp.compiler.io.Source or a fully qualified class name
   * @return  xp.compiler.task.CompilationTask
   * @throws  lang.IllegalArgumentException for argument type mismatches
   * @throws  lang.ElementNotFoundException if class given and class cannot be found
   */
  public function newSubTask($arg) {
    if ($arg instanceof Source) {
      $source= $arg;
    } else if (is_string($arg)) {
      if (!($source= $this->manager->findClass($arg))) {
        throw new ElementNotFoundException(sprintf(
          "Cannot find class %s, tried {*.%s} in [\n  %s\n]",
          $arg,
          implode(', *.', array_keys(Syntax::available())),
          implode("\n  ", $this->manager->getSourcePaths())
        ));
      }
    } else {
      throw new \lang\IllegalArgumentException('Expected either a string or a Source object');
    }
    return new self($source, $this->listener, $this->manager, $this->emitter, $this->done);
  }
  
  /**
   * Starts with a type
   *
   * @param   xp.compiler.ast.ParseTree tree
   * @return  xp.compiler.types.Types
   */
  protected function partialType(\xp\compiler\ast\ParseTree $tree) {
    return new TypeReference(
      $tree->package ? new TypeName($tree->package->name.'.'.$tree->declaration->name->name) : $tree->declaration->name,
      Types::PARTIAL_KIND,
      $tree->declaration->modifiers
    );
  }
  
  /**
   * Run this task and emit compiled code using a given emitter
   *
   * @return  xp.compiler.types.Types
   * @throws  xp.compiler.CompilationException
   */
  public function run() {
    if (!$this->done->containsKey($this->source)) {
      $scope= new \xp\compiler\types\TaskScope($this);

      // Start run
      $this->listener->compilationStarted($this->source);
      try {
        $tree= $this->manager->parseFile($this->source);
        $this->done[$this->source]= $this->partialType($tree);
        $result= $this->emitter->emit($tree, $scope);
        $target= $this->manager->getTarget($result, $this->source);
        $this->manager->write($result, $target);
        $this->listener->compilationSucceeded($this->source, $target, $this->emitter->messages());
      } catch (\text\parser\generic\ParseException $e) {
        $this->listener->parsingFailed($this->source, $e);
        throw new CompilationException('Parse error', $e);
      } catch (\lang\FormatException $e) {
        $this->listener->emittingFailed($this->source, $e);
        throw new CompilationException('Emitting error', $e);
      } catch (\io\IOException $e) {
        $this->listener->compilationFailed($this->source, $e);
        throw new CompilationException('I/O error', $e);
      } catch (\lang\Throwable $e) {
        $this->listener->compilationFailed($this->source, $e);
        throw new CompilationException('Unknown error', $e);
      }

      // Register type as done. 
      //
      // XXX TODO: Find out why cloning is necessary?! If we don't clone but instead 
      // store a reference, we end up with the value occasionally being overwritten.
      $this->done[$this->source]= clone $result->type();
    }
    return $this->done[$this->source];
  }
  
  /**
   * Creates a string representation of this compilation task
   *
   * @return  string
   */
  public function toString() {
    $s= $this->getClassName().'(source: '.$this->source->toString().")@{\n";
    foreach ($this->done->keys() as $key) {
      $s.= '  ['.$key->toString().'] '.$this->done[$key]->getClassName().'('.$this->done[$key]->name().")\n";
    }
    return $s.'}';
  }
}