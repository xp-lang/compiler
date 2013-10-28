<?php namespace xp\compiler\emit;

use xp\compiler\optimize\Optimization;
use xp\compiler\optimize\Optimizations;
use xp\compiler\checks\Checks;
use xp\compiler\checks\Check;
use xp\compiler\CompilationProfile;
use xp\compiler\types\Scope;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\Node;

/**
 * Base class for emitters
 *
 * @see      xp://xp.compiler.ast.Node
 */
abstract class Emitter extends \lang\Object implements \util\log\Traceable {
  protected $cat= null;
  protected $messages= array(
    'warnings' => array(),
    'errors'   => array()
  );
  protected $optimizations= null;
  protected $checks= null;
  protected $scope= array(null);

  /**
   * Constructor.
   *
   */
  public function __construct() {
    $this->optimizations= new Optimizations();
    $this->checks= new Checks();
  }
  
  /**
   * Set profile
   *
   * @param   xp.compiler.CompilationProfile
   */
  public function setProfile(CompilationProfile $profile) {
    $this->clearOptimizations();
    $this->clearChecks();

    foreach ($profile->warnings as $impl) {
      $this->checks->add($impl, false);
    }
    foreach ($profile->errors as $impl) {
      $this->checks->add($impl, true);
    }
    foreach ($profile->optimizations as $impl) {
      $this->optimizations->add($impl);
    }
  }

  /**
   * Adds an optimization
   *
   * @param   xp.compiler.optimize.Optimization o
   * @return  xp.compiler.optimize.Optimization
   */
  public function addOptimization(Optimization $o) {
    $this->optimizations->add($o);
    return $o;
  }
  
  /**
   * Adds an optimization
   *
   * @param   xp.compiler.optimize.Optimization o
   * @return  self this
   */
  public function withOptimization(Optimization $o) {
    $this->optimizations->add($o);
    return $this;
  }

  /**
   * Clears all optimizations
   */
  public function clearOptimizations() {
    $this->optimizations->clear();
  }

  /**
   * Adds a check
   *
   * @param   xp.compiler.checks.Checks c
   * @param   bool error
   * @return  xp.compiler.checks.Check
   */
  public function addCheck(Check $c, $error= false) {
    $this->checks->add($c, $error);
    return $c;
  }

  /**
   * Adds a check
   *
   * @param   xp.compiler.checks.Checks c
   * @param   bool error
   * @return  self this
   */
  public function withCheck(Check $c, $error= false) {
    $this->checks->add($c, $error);
    return $this;
  }

  /**
   * Clears all checks
   */
  public function clearChecks() {
    $this->checks->clear();
  }

  /**
   * Enter the given scope
   *
   * @param   xp.compiler.types.Scope
   */
  protected function enter(Scope $s) {
    array_unshift($this->scope, $this->scope[0]->enter($s));
  }

  /**
   * Leave the current scope, returning to the previous
   *
   */
  protected function leave() {
    array_shift($this->scope);
  }

  /**
   * Emit uses statements for a given list of types
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   [:bool] types
   */
  protected abstract function emitUses($b, array $types);

  /**
   * Emit invocations
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InvocationNode inv
   */
  protected abstract function emitInvocation($b, $inv);

  /**
   * Emit strings
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StringNode str
   */
  protected abstract function emitString($b, $str);

  /**
   * Emit an array (a sequence of elements with a zero-based index)
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArrayNode arr
   */
  protected abstract function emitArray($b, $arr);

  /**
   * Emit a map (a key/value pair dictionary)
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MapNode map
   */
  protected abstract function emitMap($b, $map);

  /**
   * Emit booleans
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BooleanNode const
   */
  protected abstract function emitBoolean($b, $const);

  /**
   * Emit null
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.NullNode const
   */
  protected abstract function emitNull($b, $const);

  /**
   * Emit constants
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ConstantNode const
   */
  protected abstract function emitConstant($b, $const);

  /**
   * Emit casts
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.CastNode cast
   */
  protected abstract function emitCast($b, $cast);

  /**
   * Emit integers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.IntegerNode num
   */
  protected abstract function emitInteger($b, $num);

  /**
   * Emit decimals
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DecimalNode num
   */
  protected abstract function emitDecimal($b, $num);

  /**
   * Emit hex numbers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.HexNode num
   */
  protected abstract function emitHex($b, $num);

  /**
   * Emit octal numbers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.OctalNode num
   */
  protected abstract function emitOctal($b, $num);

  /**
   * Emit a variable
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.VariableNode var
   */
  protected abstract function emitVariable($b, $var);

  /**
   * Emit a member access. Helper to emitChain()
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DynamicVariableReferenceNode access
   */
  protected abstract function emitDynamicMemberAccess($b, $access);

  /**
   * Emit static method call
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticMethodCallNode call
   */
  protected abstract function emitStaticMethodCall($b, $call);

  /**
   * Emit instance call
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InstanceCallNode call
   */
  protected abstract function emitInstanceCall($b, $call);

  /**
   * Emit method call
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MethodCallNode call
   */
  protected abstract function emitMethodCall($b, $call);

  /**
   * Emit member access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticMemberAccessNode access
   */
  protected abstract function emitStaticMemberAccess($b, $access);

  /**
   * Emit member access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MemberAccessNode access
   */
  protected abstract function emitMemberAccess($b, $access);

  /**
   * Emit array access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArrayAccessNode access
   */
  protected abstract function emitArrayAccess($b, $access);

  /**
   * Emit constant access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ConstantAccessNode access
   */
  protected abstract function emitConstantAccess($b, $access);

  /**
   * Emit class access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ClassAccessNode access
   */
  protected abstract function emitClassAccess($b, $access);

  /**
   * Emit a braced expression
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BracedExpressionNode const
   */
  protected abstract function emitBracedExpression($b, $braced);

  /**
   * Emit binary operation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BinaryOpNode bin
   */
  protected abstract function emitBinaryOp($b, $bin);

  /**
   * Emit unary operation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.UnaryOpNode un
   */
  protected abstract function emitUnaryOp($b, $un);

  /**
   * Emit ternary operator node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TernaryNode ternary
   */
  protected abstract function emitTernary($b, $ternary);

  /**
   * Emit comparison node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ComparisonNode cmp
   */
  protected abstract function emitComparison($b, $cmp);

  /**
   * Emit continue statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ContinueNode statement
   */
  protected abstract function emitContinue($b, $statement);

  /**
   * Emit break statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BreakNode statement
   */
  protected abstract function emitBreak($b, $statement);

  /**
   * Emit noop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.NoopNode statement
   */
  protected abstract function emitNoop($b, $statement);

  /**
   * Emit with statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.WithNode with
   */
  protected abstract function emitWith($b, $with);

  /**
   * Emit statements
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StatementsNode statements
   */
  protected abstract function emitStatements($b, $statements);

  /**
   * Emit foreach loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ForeachNode loop
   */
  protected abstract function emitForeach($b, $loop);

  /**
   * Emit do ... while loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DoNode loop
   */
  protected abstract function emitDo($b, $loop);

  /**
   * Emit while loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.WhileNode loop
   */
  protected abstract function emitWhile($b, $loop);

  /**
   * Emit for loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ForNode loop
   */
  protected abstract function emitFor($b, $loop);

  /**
   * Emit if statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.IfNode if
   */
  protected abstract function emitIf($b, $if);

  /**
   * Emit a switch case
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.CaseNode case
   */
  protected abstract function emitCase($b, $case);

  /**
   * Emit the switch default case
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DefaultNode default
   */
  protected abstract function emitDefault($b, $default);

  /**
   * Emit switch statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.SwitchNode switch
   */
  protected abstract function emitSwitch($b, $switch);

  /**
   * Emit a try / catch block
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TryNode try
   */
  protected abstract function emitTry($b, $try);

  /**
   * Emit an automatic resource management (ARM) block
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArmNode arm
   */
  protected abstract function emitArm($b, $arm);

  /**
   * Emit a throw node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ThrowNode throw
   */
  protected abstract function emitThrow($b, $throw);

  /**
   * Emit a finally node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.FinallyNode finally
   */
  protected abstract function emitFinally($b, $finally);

  /**
   * Emit a dynamic instance creation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DynamicInstanceCreationNode new
   */
  protected abstract function emitDynamicInstanceCreation($b, $new);

  /**
   * Emit an instance creation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InstanceCreationNode new
   */
  protected abstract function emitInstanceCreation($b, $new);

  /**
   * Emit an assignment
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.AssignmentNode assign
   */
  protected abstract function emitAssignment($b, $assign);

  /**
   * Emit an operator
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.OperatorNode method
   */
  protected abstract function emitOperator($b, $operator);

  /**
   * Emit a lambda
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.LambdaNode lambda
   */
  protected abstract function emitLambda($b, $lambda);

  /**
   * Emit a method
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MethodNode method
   */
  protected abstract function emitMethod($b, $method);

  /**
   * Emit static initializer
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticInitializerNode initializer
   */
  protected abstract function emitStaticInitializer($b, $initializer);

  /**
   * Emit a constructor
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ConstructorNode constructor
   */
  protected abstract function emitConstructor($b, $constructor);

  /**
   * Emit a class property
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.IndexerNode indexer
   */
  protected abstract function emitIndexer($b, $indexer);

  /**
   * Emit a class property
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.PropertyNode property
   */
  protected abstract function emitProperty($b, $property);

  /**
   * Emit an enum member
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.EnumMemberNode member
   */
  protected abstract function emitEnumMember($b, $member);

  /**
   * Emit a class constant
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ClassConstantNode const
   */
  protected abstract function emitClassConstant($b, $const);

  /**
   * Emit a class field
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.FieldNode field
   */
  protected abstract function emitField($b, $field);

  /**
   * Emit an enum declaration
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.EnumNode declaration
   */
  protected abstract function emitEnum($b, $declaration);

  /**
   * Emit a Interface declaration
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InterfaceNode declaration
   */
  protected abstract function emitInterface($b, $declaration);

  /**
   * Emit a class declaration
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ClassNode declaration
   */
  protected abstract function emitClass($b, $declaration);

  /**
   * Emit dynamic instanceof
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DynamicInstanceOfNode instanceof
   */
  protected abstract function emitDynamicInstanceOf($b, $instanceof);

  /**
   * Emit instanceof
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InstanceOfNode instanceof
   */
  protected abstract function emitInstanceOf($b, $instanceof);

  /**
   * Emit clone
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.CloneNode clone
   */
  protected abstract function emitClone($b, $clone);

  /**
   * Emit import
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ImportNode import
   */
  protected abstract function emitImport($b, $import);

  /**
   * Emit native import
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.NativeImportNode import
   */
  protected abstract function emitNativeImport($b, $import);

  /**
   * Emit static import
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticImportNode import
   */
  protected abstract function emitStaticImport($b, $import);

  /**
   * Emit a return statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ReturnNode new
   */
  protected abstract function emitReturn($b, $return);

  /**
   * Emit a silence node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.SilenceOperatorNode silenced
   */
  protected abstract function emitSilenceOperator($b, $silenced);
  
  /**
   * Emit a single node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.Node in
   */
  protected function emitOne($b, $in) {
    $node= $this->optimizations->optimize($in, $this->scope[0]);

    $b->position($node->position);
    $this->cat && $this->cat->debugf(
      '@%-3d Emit %s: %s',
      $node->position[0],
      $node->getClassName(),
      $node->hashCode()
    );

    try {
      $this->checks->verify($node, $this->scope[0], $this) && call_user_func(
        array($this, 'emit'.substr(get_class($node), 16, -4)),    // strlen('xp\\compiler\\ast\\'), strlen
        $b,
        $node
      );
    } catch (\lang\Error $e) {
      $this->error('0422', 'Cannot emit '.$node->getClassName().': '.$e->getMessage(), $node);
    } catch (\lang\Throwable $e) {
      $this->error('0500', $e->toString(), $node);
    }
  }

  /**
   * Emit all given nodes
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.Node[] nodes
   */
  protected abstract function emitAll($b, array $nodes);

  /**
   * Entry point
   *
   * @param   xp.compiler.ast.ParseTree tree
   * @param   xp.compiler.types.Scope scope
   * @return  xp.compiler.emit.EmitterResult
   */
  public abstract function emit(ParseTree $tree, Scope $scope);
  
  /**
   * Format a message
   *
   * @param   string code
   * @param   string message
   * @param   xp.compiler.ast.Node context
   * @return  string
   */
  protected function format($code, $message, Node $context= null) {
    if ($context) {               // Use given context node
      $pos= $context->position;
    } else {                      // Try to determine last context node from backtrace
      $pos= array(0, 0);
      foreach (create(new \lang\Throwable(null))->getStackTrace() as $element) {
        if (
          'emit' == substr($element->method, 0, 4) &&
          sizeof($element->args) > 1 &&
          $element->args[1] instanceof Node
        ) {
          $pos= $element->args[1]->position;
          break;
        }
      }
    }
    
    return sprintf('[%4s] %s at line %d, offset %d', $code, $message, $pos[0], $pos[1]);
  }

  /**
   * Clears messages
   */
  public function clearMessages() {
    $this->messages= array(
      'warnings' => array(),
      'errors'   => array()
    );
  }

  /**
   * Issue a warning
   *
   * @param   string code
   * @param   string message
   * @param   xp.compiler.ast.Node context
   */
  public function warn($code, $message, Node $context= null) {
    $message= $this->format($code, $message, $context);
    $this->cat && $this->cat->warn($message);
    $this->messages['warnings'][]= $message;
  }

  /**
   * Raise an error
   *
   * @param   string code
   * @param   string message
   * @param   xp.compiler.ast.Node context
   */
  public function error($code, $message, Node $context= null) {
    $message= $this->format($code, $message, $context);
    $this->cat && $this->cat->error($message);
    $this->messages['errors'][]= $message;
  }
  
  /**
   * Get a list of all messages
   *
   * @return  string[] messages
   */
  public function messages() {
    $r= array();
    foreach ($this->messages as $type => $messages) {
      $r+= $messages;
    }
    return $r;
  }

  /**
   * Return file extension including the leading dot
   *
   * @return  string
   */
  public function extension() {
    return \xp::CLASS_FILE_EXT;
  }
 
  /**
   * Set a trace for debugging
   *
   * @param   util.log.LogCategory cat
   */
  public function setTrace($cat) {
    $this->cat= $cat;
  }
}