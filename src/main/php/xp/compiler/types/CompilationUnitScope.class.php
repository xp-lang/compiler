<?php namespace xp\compiler\types;

/**
 * Represents the compilation unit scope
 *
 * In the following example:
 * ```php
 * import util.cmd.*;
 *
 * abstract class Command implements Runnable {
 *
 *   public function toString() {
 *     return nameof($this);
 *   }
 * }
 * ```
 *
 * ...this scope represents the import statement and the class
 * declaration.
 *
 * @see     xp://xp.compiler.ClassScope
 * @see     xp://xp.compiler.MethodScope
 */
class CompilationUnitScope extends Scope {

}
