<?php namespace xp\compiler;

use xp\compiler\checks\Check;
use xp\compiler\optimize\Optimization;

/**
 * A compilation profile defines:
 * * Checks to be treated as warnings
 * * Checks to be treated as errors
 * * Optimizations to apply
 */
class CompilationProfile extends \lang\Object {
  public $warnings= array();
  public $errors= array();
  public $optimizations= array();
  
  /**
   * Add a check that will produce a warning
   *
   * @param   xp.compiler.checks.Check check
   * @return  xp.compiler.checks.Check the added check
   */
  public function addWarning(Check $check) {
    $this->warnings[$check->getClassName()]= $check;
    return $check;
  }

  /**
   * Add a check that will produce a warning
   *
   * @param   xp.compiler.checks.Check check
   * @return  xp.compiler.profiles.CompilationProfile this
   */
  public function withWarning(Check $check) {
    $this->warnings[$check->getClassName()]= $check;
    return $this;
  }

  /**
   * Add a check that will produce an error
   *
   * @param   xp.compiler.checks.Check check
   * @return  xp.compiler.checks.Check the added check
   */
  public function addError(Check $check) {
    $this->errors[$check->getClassName()]= $check;
    return $check;
  }

  /**
   * Add a check that will produce an error
   *
   * @param   xp.compiler.checks.Check check
   * @return  xp.compiler.profiles.CompilationProfile this
   */
  public function withError(Check $check) {
    $this->errors[$check->getClassName()]= $check;
    return $this;
  }
  
  /**
   * Add an optimization
   *
   * @param   xp.compiler.optimize.Optimization optimization
   * @return  xp.compiler.optimize.Optimization the added optimization
   */
  public function addOptimization(Optimization $optimization) {
    $this->optimizations[$optimization->getClassName()]= $optimization;
    return $optimization;
  }

  /**
   * Add an optimization
   *
   * @param   xp.compiler.optimize.Optimization optimization
   * @return  xp.compiler.profiles.CompilationProfile this
   */
  public function withOptimization(Optimization $optimization) {
    $this->optimizations[$optimization->getClassName()]= $optimization;
    return $this;
  }
}
