<?php namespace xp\compiler\emit\php;

use xp\compiler\types\CompiledType;
use xp\compiler\types\TypeDeclaration;
use xp\compiler\types\TypeInstance;
use xp\compiler\types\TypeName;
use xp\compiler\types\Types;
use xp\compiler\types\Scope;
use xp\compiler\types\CompilationUnitScope;
use xp\compiler\types\TypeDeclarationScope;
use xp\compiler\types\MethodScope;
use xp\compiler\types\Method;
use xp\compiler\types\Field;
use xp\compiler\types\Constructor;
use xp\compiler\types\Property;
use xp\compiler\types\Operator;
use xp\compiler\types\Indexer;
use xp\compiler\types\Constant;
use xp\compiler\ast\ParseTree;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\TypeDeclarationNode;
use xp\compiler\ast\Resolveable;
use xp\compiler\ast\ArrayAccessNode;
use xp\compiler\ast\StaticMemberAccessNode;
use xp\compiler\ast\MethodCallNode;
use xp\compiler\ast\MemberAccessNode;
use xp\compiler\ast\StaticMethodCallNode;
use xp\compiler\ast\FinallyNode;
use xp\compiler\ast\CatchNode;
use xp\compiler\ast\ThrowNode;
use xp\compiler\ast\ClassNode;
use xp\compiler\ast\AssignmentNode;
use xp\compiler\ast\ArrayNode;
use xp\compiler\ast\FieldNode;
use xp\compiler\ast\ConstructorNode;
use xp\compiler\ast\MethodNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\EnumMemberNode;
use xp\compiler\ast\IndexerNode;
use xp\compiler\ast\StaticInitializerNode;
use xp\compiler\ast\LocalsToMemberPromoter;
use xp\compiler\emit\Buffer;
use lang\reflect\Modifiers;
use lang\Throwable;

/**
 * Emits sourcecode using PHP sourcecode
 *
 * @test     xp://net.xp_lang.tests.execution.source.AnnotationTest
 * @test     xp://net.xp_lang.tests.execution.source.ArrayTest
 * @test     xp://net.xp_lang.tests.execution.source.AssignmentTest
 * @test     xp://net.xp_lang.tests.execution.source.AutoPropertiesTest
 * @test     xp://net.xp_lang.tests.execution.source.CastingTest
 * @test     xp://net.xp_lang.tests.execution.source.CatchTest
 * @test     xp://net.xp_lang.tests.execution.source.ChainingTest
 * @test     xp://net.xp_lang.tests.execution.source.ClassDeclarationTest
 * @test     xp://net.xp_lang.tests.execution.source.ComparisonTest
 * @test     xp://net.xp_lang.tests.execution.source.ConcatTest
 * @test     xp://net.xp_lang.tests.execution.source.DefaultArgsTest
 * @test     xp://net.xp_lang.tests.execution.source.EnumDeclarationTest
 * @test     xp://net.xp_lang.tests.execution.source.ExecutionTest
 * @test     xp://net.xp_lang.tests.execution.source.ExtensionMethodsTest
 * @test     xp://net.xp_lang.tests.execution.source.Filter
 * @test     xp://net.xp_lang.tests.execution.source.FinallyTest
 * @test     xp://net.xp_lang.tests.execution.source.Functions
 * @test     xp://net.xp_lang.tests.execution.source.InstanceCreationTest
 * @test     xp://net.xp_lang.tests.execution.source.InterfaceDeclarationTest
 * @test     xp://net.xp_lang.tests.execution.source.LambdaTest
 * @test     xp://net.xp_lang.tests.execution.source.LoopExecutionTest
 * @test     xp://net.xp_lang.tests.execution.source.MathTest
 * @test     xp://net.xp_lang.tests.execution.source.MemberInitTest
 * @test     xp://net.xp_lang.tests.execution.source.MethodOverloadingTest
 * @test     xp://net.xp_lang.tests.execution.source.MultiCatchTest
 * @test     xp://net.xp_lang.tests.execution.source.NativeClassUsageTest
 * @test     xp://net.xp_lang.tests.execution.source.NavigationOperatorTest
 * @test     xp://net.xp_lang.tests.execution.source.OperatorOverloadingTest
 * @test     xp://net.xp_lang.tests.execution.source.OperatorTest
 * @test     xp://net.xp_lang.tests.execution.source.PropertiesTest
 * @test     xp://net.xp_lang.tests.execution.source.StaticImportTest
 * @test     xp://net.xp_lang.tests.execution.source.StringBuffer
 * @test     xp://net.xp_lang.tests.execution.source.TernaryOperatorTest
 * @test     xp://net.xp_lang.tests.execution.source.VarArgsTest
 * @test     xp://net.xp_lang.tests.execution.source.VariablesTest
 * @test     xp://net.xp_lang.tests.execution.source.WithTest
 * @test     xp://net.xp_lang.tests.compilation.TypeTest
 * @see      xp://xp.compiler.ast.ParseTree
 */
abstract class Emitter extends \xp\compiler\emit\Emitter {
  protected 
    $temp         = array(null),
    $method       = array(null),
    $finalizers   = array(null),
    $metadata     = array(null),
    $properties   = array(null),
    $inits        = array(null),
    $local        = array(null),
    $types        = array(null);

  /**
   * Returns the literal for a given type
   *
   * @param  xp.compiler.types.Types t
   * @param  bool base Whether to use only the base type
   * @return string
   */
  protected abstract function literal($t, $base= false);

  /**
   * Returns the literal for a given type
   *
   * @param  xp.compiler.types.TypeName t
   * @return string
   */
  protected abstract function declaration($t);

  /**
   * Emit type name and modifiers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   string type
   * @param   xp.compiler.ast.TypeDeclarationNode declaration
   */
  protected function emitTypeName($b, $type, TypeDeclarationNode $declaration, $prefix= '') {
    $this->metadata[0]['class']= array();
    $declaration->literal= $this->declaration($declaration->name);

    // Emit abstract and final modifiers
    if (Modifiers::isAbstract($declaration->modifiers)) {
      $b->append('abstract ');
    } else if (Modifiers::isFinal($declaration->modifiers)) {
      $b->append('final ');
    }

    // Emit declaration
    $b->append($type)->append(' ')->append($prefix.$declaration->name->name);
  }

  /**
   * Returns a new temporary variable
   *
   * @return  string
   */
  protected function tempVar() {
    return '$T'.($this->temp[0]++);
  }
  
  /**
   * Check whether a node is writeable - that is: can be the left-hand
   * side of an assignment
   *
   * @param   xp.compiler.ast.Node node
   * @return  bool
   */
  protected function isWriteable($node) {
    if ($node instanceof VariableNode || $node instanceof ArrayAccessNode) {
      return true;
    } else if ($node instanceof MemberAccessNode || $node instanceof StaticMemberAccessNode) {
      return true;    // TODO: Check for private, protected
    }
    return false;
  }
  
  /**
   * Creates a generic component string for use in meta data
   *
   * @param   xp.compiler.types.TypeName type
   * @return  string
   */
  protected function genericComponentAsMetadata($type) {
    $s= '';
    foreach ($type->components as $component) {
      $s.= ', '.$component->name;
    }
    return substr($s, 2);
  }
  
  /**
   * Emit uses statements for a given list of types
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   [:bool] types
   */
  protected function emitUses($b, array $types) {
    raise('lang.MethodNotImplementedException', 'Overwritten in subclasses', __METHOD__);
  }
  
  /**
   * Emit parameters
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.Node[] params
   * @param   bool brackets
   * @return  int
   */
  protected function emitInvocationArguments($b, array $params, $brackets= true) {
    $brackets && $b->append('(');
    $s= sizeof($params)- 1;
    foreach ($params as $i => $param) {
      $this->emitOne($b, $param);
      $i < $s && $b->append(',');
    }
    $brackets && $b->append(')');
    return sizeof($params);
  }
  
  /**
   * Emit invocations
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InvocationNode inv
   */
  protected function emitInvocation($b, $inv) {
    if (!isset($this->scope[0]->statics[$inv->name])) {
      if (!($resolved= $this->scope[0]->resolveStatic($inv->name))) {
        $this->error('T501', 'Cannot resolve '.$inv->name.'()', $inv);
        return;
      }
      $this->scope[0]->statics[$inv->name]= $resolved;         // Cache information
    }
    $ptr= $this->scope[0]->statics[$inv->name];

    // Static method call vs. function call
    if (true === $ptr) {
      $b->append($inv->name);
      $this->emitInvocationArguments($b, (array)$inv->arguments);
      $this->scope[0]->setType($inv, TypeName::$VAR);
    } else {
      $b->append($this->literal($ptr->holder).'::'.$ptr->name());
      $this->emitInvocationArguments($b, (array)$inv->arguments);
      $this->scope[0]->setType($inv, $ptr->returns);
    }
  }
  
  /**
   * Emit strings
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StringNode str
   */
  protected function emitString($b, $str) {
    $b->append("'");
    $b->append(strtr($str->resolve(), array(
      "'"   => "\'",
      '\\'  => '\\\\'
    )));
    $b->append("'");
  }

  /**
   * Emit an array (a sequence of elements with a zero-based index)
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArrayNode arr
   */
  protected function emitArray($b, $arr) {
    $b->append('array(');
    foreach ((array)$arr->values as $value) {
      $this->emitOne($b, $value);
      $b->append(',');
    }
    $b->append(')');
  }

  /**
   * Emit a map (a key/value pair dictionary)
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MapNode map
   */
  protected function emitMap($b, $map) {
    $b->append('array(');
    foreach ((array)$map->elements as $pair) {
      $this->emitOne($b, $pair[0]);
      $b->append(' => ');
      $this->emitOne($b, $pair[1]);
      $b->append(',');
    }
    $b->append(')');
  }

  /**
   * Emit booleans
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BooleanNode const
   */
  protected function emitBoolean($b, $const) {
    $b->append($const->resolve() ? 'TRUE' : 'FALSE');
  }

  /**
   * Emit null
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.NullNode const
   */
  protected function emitNull($b, $const) {
    $b->append('NULL');
  }
  
  /**
   * Emit constants
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ConstantNode const
   */
  protected function emitConstant($b, $const) {
    if ($constant= $this->scope[0]->resolveConstant($const->name)) {
      $b->append(var_export($constant->value, true));
      return;
    }

    $b->append($const->name);
  }

  /**
   * Emit \casts
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.CastNode cast
   */
  protected function emitCast($b, $cast) {
    static $primitives= array(
      'int'     => '(int)',
      'double'  => '(double)',
      'string'  => '(string)',
      'array'   => '(array)',
      'bool'    => '(bool)',
      // Missing intentionally: object and unset \casts
    );

    if (!$cast->check) {
      $this->emitOne($b, $cast->expression);
    } else if ($cast->type->isPrimitive()) {
      $b->append($primitives[$cast->type->name]);
      $this->emitOne($b, $cast->expression);
    } else if ($cast->type->isArray() || $cast->type->isMap()) {
      $b->append('(array)');
      $this->emitOne($b, $cast->expression);
    } else {
      $b->append('cast(');
      $this->emitOne($b, $cast->expression);
      $b->append(', \'')->append($this->resolveType($cast->type)->name())->append('\')');
    }
    
    $this->scope[0]->setType($cast, $cast->type);
  }

  /**
   * Emit integers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.IntegerNode num
   */
  protected function emitInteger($b, $num) {
    $b->append($num->resolve());
  }

  /**
   * Emit decimals
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DecimalNode num
   */
  protected function emitDecimal($b, $num) {
    $res= $num->resolve();
    $b->append($res);
    
    // Prevent float(2) being dumped as "2" and thus an int literal
    strstr($res, '.') || $b->append('.0');
  }

  /**
   * Emit hex numbers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.HexNode num
   */
  protected function emitHex($b, $num) {
    $b->append($num->resolve());
  }

  /**
   * Emit octal numbers
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.OctalNode num
   */
  protected function emitOctal($b, $num) {
    $b->append($num->resolve());
  }
  
  /**
   * Emit a variable
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.VariableNode var
   */
  protected function emitVariable($b, $var) {
    $b->append('$'.$var->name);
  }

  /**
   * Emit a member access. Helper to emitChain()
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DynamicVariableReferenceNode access
   */
  public function emitDynamicMemberAccess($b, $access) {
    $this->emitOne($b, $call->target);

    $b->append('->{');
    $this->emitOne($b, $access->expression);
    $b->append('}');
    
    $this->scope[0]->setType($call, TypeName::$VAR);
  }

  /**
   * Emit static method call
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticMethodCallNode call
   */
  public function emitStaticMethodCall($b, $call) {
    $ptr= $this->resolveType($call->type);
    $b->append($this->literal($ptr).'::'.$call->name);
    $this->emitInvocationArguments($b, (array)$call->arguments);

    // Record type
    $this->scope[0]->setType($call, $ptr->hasMethod($call->name) ? $ptr->getMethod($call->name)->returns : TypeName::$VAR);
  }

  /**
   * Emit instance call
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InstanceCallNode call
   */
  public function emitInstanceCall($b, $call) {

    // Navigation operator
    if ($call->nav) {
      $var= $this->tempVar();
      $b->append('(null === ('.$var.'=');
      $this->emitOne($b, $call->target);
      $b->append(') ? null : call_user_func(')->append($var);
      if ($call->arguments) {
        $b->append(', ');
        $this->emitInvocationArguments($b, $call->arguments, false);
      }
      $b->append('))');
    } else {
      $b->append('call_user_func(');
      $this->emitOne($b, $call->target);
      if ($call->arguments) {
        $b->append(', ');
        $this->emitInvocationArguments($b, $call->arguments, false);
      }
      $b->append(')');
    }
  }

  /**
   * Emit method call
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MethodCallNode call
   */
  public function emitMethodCall($b, $call) {
    $mark= $b->mark();
    $this->emitOne($b, $call->target);
    
    // Check for extension methods
    $ptr= new TypeInstance($this->resolveType($this->scope[0]->typeOf($call->target), false));
    if (null !== ($ext= $this->scope[0]->getExtension($ptr, $call->name))) {
      $b->insert($this->literal($ext->holder).'::'.$call->name.'(', $mark);
      if ($call->arguments) {
        $b->append(', ');
        $this->emitInvocationArguments($b, $call->arguments, false);
      }
      $b->append(')');
      $this->scope[0]->setType($call, $ext->returns);
      return;
    }

    // Manually verify as we can then rely on call target type being available
    if (!$this->checks->verify($call, $this->scope[0], $this, true)) return;

    if ($call->nav) {
      $var= $this->tempVar();
      $b->insert('(NULL === ('.$var.'=', $mark);
      $b->append(') ? NULL : ')->append($var)->append('->');
      $b->append($call->name);
      $this->emitInvocationArguments($b, (array)$call->arguments);
      $b->append(')');
    } else {

      // Rewrite for unsupported syntax
      // - new Date().toString() to create(new Date()).toString()
      // - (<expr>).toString to create(<expr>).toString()
      if (
        !$call->target instanceof ArrayAccessNode && 
        !$call->target instanceof MethodCallNode &&
        !$call->target instanceof MemberAccessNode &&
        !$call->target instanceof VariableNode &&
        !$call->target instanceof StaticMemberAccessNode &&
        !$call->target instanceof StaticMethodCallNode
      ) {
        $b->insert('create(', $mark);
        $b->append(')');
      }

      $b->append('->'.$call->name);
      $this->emitInvocationArguments($b, (array)$call->arguments);
    }

    // Record type
    $this->scope[0]->setType($call, $ptr->hasMethod($call->name) ? $ptr->getMethod($call->name)->returns : TypeName::$VAR);
  }

  /**
   * Emit member access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticMemberAccessNode access
   */
  public function emitStaticMemberAccess($b, $access) {
    $ptr= $this->resolveType($access->type);
    $b->append($this->literal($ptr).'::$'.$access->name);

    // Record type
    $this->scope[0]->setType($access, $ptr->hasField($access->name) ? $ptr->getField($access->name)->type : TypeName::$VAR);
  }

  /**
   * Emit member access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MemberAccessNode access
   */
  public function emitMemberAccess($b, $access) {
    $mark= $b->mark();
    $this->emitOne($b, $access->target);
    
    $type= $this->scope[0]->typeOf($access->target);
    
    // Overload [...].length
    if ($type->isArray() && 'length' === $access->name) {
      $b->insert('sizeof(', $mark);
      $b->append(')');
      $this->scope[0]->setType($access, new TypeName('int'));
      return;
    }

    // Manually verify as we can then rely on call target type being available
    if (!$this->checks->verify($access, $this->scope[0], $this, true)) return;

    // Navigation operator
    if ($access->nav) {
      $var= $this->tempVar();
      $b->insert('(NULL === ('.$var.'=', $mark);
      $b->append(') ? NULL : ')->append($var)->append('->');
      $b->append($access->name);
      $b->append(')');
    } else {

      // Rewrite for unsupported syntax
      // - new Person().name to create(new Person()).name
      // - (<expr>).name to create(<expr>).name
      if (
        !$access->target instanceof ArrayAccessNode && 
        !$access->target instanceof MethodCallNode &&
        !$access->target instanceof MemberAccessNode &&
        !$access->target instanceof VariableNode &&
        !$access->target instanceof StaticMemberAccessNode &&
        !$access->target instanceof StaticMethodCallNode
      ) {
        $b->insert('create(', $mark);
        $b->append(')');
      }

      $b->append('->'.$access->name);
    }
    
    // Record type
    $ptr= new TypeInstance($this->resolveType($type));
    if ($ptr->hasField($access->name)) {
      $result= $ptr->getField($access->name)->type;
    } else if ($ptr->hasProperty($access->name)) {
      $result= $ptr->getProperty($access->name)->type;
    } else {
      $result= TypeName::$VAR;
    }
    $this->scope[0]->setType($access, $result);
  }

  /**
   * Emit array access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArrayAccessNode access
   */
  public function emitArrayAccess($b, $access) {
    $mark= $b->mark();
    $this->emitOne($b, $access->target);
    
    // Manually verify as we can then rely on call target type being available
    if (!$this->checks->verify($access, $this->scope[0], $this, true)) return;
    
    // Rewrite for unsupported syntax
    // - $a.getMethods()[2] to this($a.getMethods(), 2)
    // - T::asList()[2] to this(T::asList(), 2)
    // - new int[]{5, 6, 7}[2] to this(array(5, 6, 7), 2)
    if (
      !$access->target instanceof ArrayAccessNode && 
      !$access->target instanceof MemberAccessNode &&
      !$access->target instanceof VariableNode &&
      !$access->target instanceof StaticMemberAccessNode
    ) {
      $b->insert('this(', $mark);
      $b->append(',');
      $this->emitOne($b, $access->offset);
      $b->append(')');
    } else {
      $b->append('[');
      $access->offset && $this->emitOne($b, $access->offset);
      $b->append(']');
    }
  }

  /**
   * Emit constant access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ConstantAccessNode access
   */
  public function emitConstantAccess($b, $access) {
    $ptr= $this->resolveType($access->type);
    $b->append($this->literal($ptr).'::'.$access->name);

    // Record type
    $this->scope[0]->setType($access, $ptr->hasConstant($access->name) ? $ptr->getConstant($access->name)->type : TypeName::$VAR);
  }

  /**
   * Emit class access
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ClassAccessNode access
   */
  public function emitClassAccess($b, $access) {
    $ptr= $this->resolveType($access->type);
    $b->append('XPClass::forName(\''.$ptr->name().'\')');

    // Record type
    $this->scope[0]->setType($access, new TypeName('lang.XPClass'));
  }

  /**
   * Emit a braced expression
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BracedExpressionNode const
   */
  protected function emitBracedExpression($b, $braced) {
    $b->append('(');
    $this->emitOne($b, $braced->expression);
    $b->append(')');
  }

  /**
   * Emit binary operation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BinaryOpNode bin
   */
  protected function emitBinaryOp($b, $bin) {
    static $ops= array(
      '~'   => array(true, '.'),
      '-'   => array(true, '-'),
      '+'   => array(true, '+'),
      '*'   => array(true, '*'),
      '/'   => array(true, '/'),
      '%'   => array(true, '%'),
      '|'   => array(true, '|'),
      '&'   => array(true, '&'),
      '^'   => array(true, '^'),
      '&&'  => array(true, '&&'),
      '||'  => array(true, '||'),
      '>>'  => array(true, '>>'),
      '<<'  => array(true, '<<'),
      '**'  => array(false, 'pow')
    );
    static $ovl= array(
      '~'   => 'concat',
      '-'   => 'minus',
      '+'   => 'plus',
      '*'   => 'times',
      '/'   => 'div',
      '%'   => 'mod',
    );
    
    $t= $this->scope[0]->typeOf($bin->lhs);
    if ($t->isClass()) {
      $ptr= $this->resolveType($t);
      if ($ptr->hasOperator($bin->op)) {
        $o= $ptr->getOperator($bin->op);
        $b->append($this->literal($ptr));
        $b->append('::operator··')->append($ovl[$bin->op])->append('(');
        $this->emitOne($b, $bin->lhs);
        $b->append(',');
        $this->emitOne($b, $bin->rhs);
        $b->append(')');

        $this->scope[0]->setType($bin, $o->returns);
        return;
      }
    }

    $o= $ops[$bin->op];
    if ($o[0]) {        // infix
      $this->emitOne($b, $bin->lhs);
      $b->append($o[1]);
      $this->emitOne($b, $bin->rhs);
    } else {
      $b->append($o[1])->append('(');
      $this->emitOne($b, $bin->lhs);
      $b->append(',');
      $this->emitOne($b, $bin->rhs);
      $b->append(')');
    }
  }

  /**
   * Emit unary operation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.UnaryOpNode un
   */
  protected function emitUnaryOp($b, $un) {
    static $ops= array(
      '++'   => '++',
      '--'   => '--',
    );
    
    if ('!' === $un->op) {      // FIXME: Use NotNode for this?
      $b->append('!');
      $this->emitOne($b, $un->expression);
      return;
    } else if ('-' === $un->op) {
      $b->append('-');
      $this->emitOne($b, $un->expression);
      return;
    } else if ('~' === $un->op) {
      $b->append('~');
      $this->emitOne($b, $un->expression);
      return;
    } else if (!$this->isWriteable($un->expression)) {
      $this->error('U400', 'Cannot perform unary '.$un->op.' on '.$un->expression->getClassName(), $un);
      return;
    }

    if ($un->postfix) {
      $this->emitOne($b, $un->expression);
      $b->append($ops[$un->op]);
    } else {
      $b->append($ops[$un->op]);
      $this->emitOne($b, $un->expression);
    }
  }

  /**
   * Emit ternary operator node
   *
   * Note: The following two are equivalent:
   * <code>
   *   $a= $b ?: $c;
   *   $a= $b ? $b : $c;
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TernaryNode ternary
   */
  protected function emitTernary($b, $ternary) {
    $this->emitOne($b, $ternary->condition);
    $b->append('?');
    $this->emitOne($b, $ternary->expression ?: $ternary->condition);
    $b->append(':');
    $this->emitOne($b, $ternary->conditional);
  }

  /**
   * Emit comparison node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ComparisonNode cmp
   */
  protected function emitComparison($b, $cmp) {
    static $ops= array(
      '=='   => '==', 
      '==='  => '===',
      '!='   => '!=', 
      '!=='  => '!==',
      '<='   => '<=', 
      '<'    => '<',  
      '>='   => '>=', 
      '>'    => '>',  
    );

    $this->emitOne($b, $cmp->lhs);
    $b->append(' '.$ops[$cmp->op].' ');
    $this->emitOne($b, $cmp->rhs);
  }

  /**
   * Emit continue statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ContinueNode statement
   */
  protected function emitContinue($b, $statement) {
    $b->append('continue');
  }

  /**
   * Emit break statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.BreakNode statement
   */
  protected function emitBreak($b, $statement) {
    $b->append('break');
  }

  /**
   * Emit noop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.NoopNode statement
   */
  protected function emitNoop($b, $statement) {
    // NOOP
  }

  /**
   * Emit with statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.WithNode with
   */
  protected function emitWith($b, $with) {
    $this->emitAll($b, $with->assignments);
    $this->emitAll($b, $with->statements);
  }

  /**
   * Emit statements
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StatementsNode statements
   */
  protected function emitStatements($b, $statements) {
    $this->emitAll($b, (array)$statements->list);
  }

  /**
   * Emit foreach loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ForeachNode loop
   */
  protected function emitForeach($b, $loop) {
    $b->append('foreach (');
    $this->emitOne($b, $loop->expression);
    
    // Assign key and value types by checking for loop expression's type
    // * var type may be enumerable
    // * any other type may define an overlad
    $t= $this->scope[0]->typeOf($loop->expression);
    if ($t->isVariable()) {
      $this->warn('T203', 'Enumeration of (var)'.$loop->expression->hashCode().' verification deferred until runtime', $loop);
      $vt= TypeName::$VAR;
      $kt= new TypeName('int');
    } else {
      $ptr= $this->resolveType($t);
      if (!$ptr->isEnumerable()) {
        $this->warn('T300', 'Type '.$ptr->name().' is not enumerable in loop expression '.$loop->expression->getClassName().'['.$loop->expression->hashCode().']', $loop);
        $vt= TypeName::$VAR;
        $kt= new TypeName('int');
      } else {
        $enum= $ptr->getEnumerator();
        $vt= $enum->value;
        $kt= $enum->key;
      }
    }

    $b->append(' as ');
    if (isset($loop->assignment['key'])) {
      $b->append('$'.$loop->assignment['key'].' => ');
      $this->scope[0]->setType(new VariableNode($loop->assignment['key']), $kt);
    }
    $b->append('$'.$loop->assignment['value'].') {');
    $this->scope[0]->setType(new VariableNode($loop->assignment['value']), $vt);
    $this->emitAll($b, (array)$loop->statements);
    $b->append('}');
  }

  /**
   * Emit do ... while loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DoNode loop
   */
  protected function emitDo($b, $loop) {
    $b->append('do {');
    $this->emitAll($b, (array)$loop->statements);
    $b->append('} while (');
    $this->emitOne($b, $loop->expression);
    $b->append(');');
  }

  /**
   * Emit while loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.WhileNode loop
   */
  protected function emitWhile($b, $loop) {
    $b->append('while (');
    $this->emitOne($b, $loop->expression);
    $b->append(') {');
    $this->emitAll($b, (array)$loop->statements);
    $b->append('}');
  }
  
  /**
   * Emit components inside a for() statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @return  xp.compiler.ast.Node[] nodes
   */
  protected function emitForComponent($b, array $nodes) {
    $s= sizeof($nodes)- 1;
    foreach ($nodes as $i => $node) {
      $this->emitOne($b, $node);
      $i < $s && $b->append(', ');
    }
  }

  /**
   * Emit for loop
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ForNode loop
   */
  protected function emitFor($b, $loop) {
    $b->append('for (');
    $this->emitForComponent($b, (array)$loop->initialization);
    $b->append(';');
    $this->emitForComponent($b, (array)$loop->condition);
    $b->append(';');
    $this->emitForComponent($b, (array)$loop->loop);
    $b->append(') {');
    $this->emitAll($b, (array)$loop->statements);
    $b->append('}');
  }
  
  /**
   * Emit if statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.IfNode if
   */
  protected function emitIf($b, $if) {
    $b->append('if (');
    $this->emitOne($b, $if->condition);
    $b->append(') {');
    $this->emitAll($b, (array)$if->statements);
    $b->append('}');
    if ($if->otherwise) {
      $b->append('else {');
      $this->emitAll($b, (array)$if->otherwise->statements);
      $b->append('}');
    }
  }

  /**
   * Emit a switch case
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.CaseNode case
   */
  protected function emitCase($b, $case) {
    $b->append('case ');
    $this->emitOne($b, $case->expression);
    $b->append(': ');
    $this->emitAll($b, (array)$case->statements);
  }

  /**
   * Emit the switch default case
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DefaultNode default
   */
  protected function emitDefault($b, $default) {
    $b->append('default: ');
    $this->emitAll($b, (array)$default->statements);
  }

  /**
   * Emit switch statement
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.SwitchNode switch
   */
  protected function emitSwitch($b, $switch) {
    $b->append('switch (');
    $this->emitOne($b, $switch->expression);
    $b->append(') {');
    $this->emitAll($b, (array)$switch->cases);
    $b->append('}');
  }
  
  /**
   * Emit a try / catch block
   * 
   * Simple form:
   * <code>
   *   try {
   *     // [...statements...]
   *   } catch (lang.Throwable $e) {
   *     // [...error handling...]
   *   }
   * </code>
   *
   * Multiple catches:
   * <code>
   *   try {
   *     // [...statements...]
   *   } catch (lang.IllegalArgumentException $e) {
   *     // [...error handling for IAE...]
   *   } catch (lang.FormatException $e) {
   *     // [...error handling for FE...]
   *   }
   * </code>
   *
   * Try/finally without catch:
   * <code>
   *   try {
   *     // [...statements...]
   *   } finally {
   *     // [...finalizations...]
   *   }
   * </code>
   *
   * Try/finally with catch:
   * <code>
   *   try {
   *     // [...statements...]
   *   } catch (lang.Throwable $e) {
   *     // [...error handling...]
   *   } finally {
   *     // [...finalizations...]
   *   }
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TryNode try
   */
  protected function emitTry($b, $try) {
    static $mangled= '··e';

    // Check whether a finalization handler is available. If so, because
    // the underlying runtime does not support this, add statements after
    // the try block and to all catch blocks
    $numHandlers= sizeof($try->handling);
    if ($try->handling[$numHandlers- 1] instanceof FinallyNode) {
      array_unshift($this->finalizers, array_pop($try->handling));
      $numHandlers--;
    } else {
      array_unshift($this->finalizers, null);
    }
    
    // If no handlers are left, create a simple catch-all-and-rethrow
    // handler
    if (0 == $numHandlers) {
      $rethrow= new ThrowNode(array('expression' => new VariableNode($mangled)));
      $first= new CatchNode(array(
        'type'       => new TypeName('lang.Throwable'),
        'variable'   => $mangled,
        'statements' => $this->finalizers[0] ? array($this->finalizers[0], $rethrow) : array($rethrow)
      ));
    } else {
      $first= $try->handling[0];
      $this->scope[0]->setType(new VariableNode($first->variable), $first->type);
    }

    $b->append('try {'); {
      $this->emitAll($b, (array)$try->statements);
      $this->finalizers[0] && $this->emitOne($b, $this->finalizers[0]);
    }
    
    // First catch.
    $b->append('} catch('.$this->literal($this->resolveType($first->type)).' $'.$first->variable.') {'); {
      $this->scope[0]->setType(new VariableNode($first->variable), $first->type);
      $this->emitAll($b, (array)$first->statements);
      $this->finalizers[0] && $this->emitOne($b, $this->finalizers[0]);
    }
    
    // Additional catches
    for ($i= 1; $i < $numHandlers; $i++) {
      $b->append('} catch('.$this->literal($this->resolveType($try->handling[$i]->type)).' $'.$try->handling[$i]->variable.') {'); {
        $this->scope[0]->setType(new VariableNode($try->handling[$i]->variable), $try->handling[$i]->type);
        $this->emitAll($b, (array)$try->handling[$i]->statements);
        $this->finalizers[0] && $this->emitOne($b, $this->finalizers[0]);
      }
    }
    
    $b->append('}');
    array_shift($this->finalizers);
  }

  /**
   * Emit an automatic resource management (ARM) block
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ArmNode arm
   */
  protected function emitArm($b, $arm) {
    static $mangled= '··e';
    static $ignored= '··i';

    $this->emitAll($b, $arm->initializations);

    // Manually verify as we can then rely on call target type being available
    if (!$this->checks->verify($arm, $this->scope[0], $this, true)) return;

    $b->append('$'.$mangled.'= NULL; try {');
    $this->emitAll($b, (array)$arm->statements);
    $b->append('} catch (Exception $'.$mangled.') {}');
    foreach ($arm->variables as $v) {
      $b->append('try { $')->append($v->name)->append('->close(); } catch (Exception $'.$ignored.') {}');
    }
    $b->append('if ($'.$mangled.') throw $'.$mangled.';'); 
  }
  
  /**
   * Emit a throw node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ThrowNode throw
   */
  protected function emitThrow($b, $throw) {
    $b->append('throw ');
    $this->emitOne($b, $throw->expression);
  }

  /**
   * Emit a finally node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.FinallyNode finally
   */
  protected function emitFinally($b, $finally) {
    $this->emitAll($b, (array)$finally->statements);
  }

  /**
   * Emit a dynamic instance creation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DynamicInstanceCreationNode new
   */
  protected function emitDynamicInstanceCreation($b, $new) {
    $b->append('new ')->append('$')->append($new->variable);
    $this->emitInvocationArguments($b, (array)$new->parameters);
    
    $this->scope[0]->setType($new, new TypeName('lang.Object'));
  }

  /**
   * Emit an instance creation node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InstanceCreationNode new
   */
  protected function emitInstanceCreation($b, $new) {

    // Anonymous instance creation:
    //
    // - Create unique classname
    // - Extend parent class if type is a class
    // - Implement type and extend lang.Object if it's an interface 
    //
    // Do not register type name from new(), it will be added by 
    // emitClass() during declaration emittance.
    $generic= null;
    if (isset($new->body)) {
      $parent= $this->resolveType($new->type, false);
      if (Types::INTERFACE_KIND === $parent->kind()) {
        $p= array('parent' => new TypeName('lang.Object'), 'implements' => array($new->type));
        
        // If the interface we implement is generic, we need to
        // make the generated class a generic instance.
        if ($new->type->components) {
          $components= array();
          foreach ($new->type->components as $component) {
            $components[]= $this->resolveType($component, false)->name();
          }
          $generic= array($parent->name(), null, $components);
        }
        
      } else if (Types::ENUM_KIND === $parent->kind()) {
        $this->error('C405', 'Cannot create anonymous enums', $new);
        return;
      } else {
        $p= array('parent' => $new->type, 'implements' => null);
      }

      $unique= new TypeName(strtr($this->literal($parent), '\\', '¦').'··'.uniqid());
      $decl= new ClassNode(0, null, $unique, $p['parent'], $p['implements'], $new->body);
      $decl->synthetic= true;
      $generic && $decl->generic= $generic;
      $ptr= new TypeDeclaration(new ParseTree(null, array(), $decl), $parent);
      $this->scope[0]->declarations[]= $decl;
      $this->scope[0]->setType($new, $unique);
    } else {
      $ptr= $this->resolveType($new->type);
      $this->scope[0]->setType($new, $new->type);
    }
    
    // If generic instance is created, use the create(spec, args*)
    // core functionality. If this a compiled generic type we may
    // do quite a bit better - but how do we detect this?
    if ($new->type->components && !$generic) {
      $b->append('create(\'new '.$ptr->name().'<');
      $s= sizeof($new->type->components)- 1;
      foreach ($new->type->components as $i => $component) {
        $b->append($this->resolveType($component)->name());
        $i < $s && $b->append(',');
      }
      $b->append('>\'');
      if ($new->parameters) {
        $b->append(',');
        $this->emitInvocationArguments($b, (array)$new->parameters, false);
      }
      $b->append(')');
    } else {
      $b->append('new '.$this->literal($ptr));
      $this->emitInvocationArguments($b, (array)$new->parameters);
    }
  }
  
  /**
   * Emit an assignment
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.AssignmentNode assign
   */
  protected function emitAssignment($b, $assign) {
    static $ops= array(
      '='    => '=', 
      '~='   => '.=',
      '-='   => '-=',
      '+='   => '+=',
      '*='   => '*=',
      '/='   => '/=',
      '%='   => '%=',
      '|='   => '|=',
      '^='   => '^=',
      '&='   => '&=',
      '<<='  => '<<=',
      '>>='  => '>>=',
    );

    static $ovl= array(
      '~='   => 'concat',
      '-='   => 'minus',
      '+='   => 'plus',
      '*='   => 'times',
      '/='   => 'div',
      '%='   => 'mod',
    );

    $t= $this->scope[0]->typeOf($assign->variable);
    if ($t->isClass()) {
      $ptr= $this->resolveType($t);
      if ($ptr->hasOperator($assign->op{0})) {
        $o= $ptr->getOperator($assign->op{0});
        
        $this->emitOne($b, $assign->variable);
        $b->append('=');
        $b->append($this->literal($ptr));
        $b->append('::operator··')->append($ovl[$assign->op])->append('(');
        $this->emitOne($b, $assign->variable);
        $b->append(',');
        $this->emitOne($b, $assign->expression);
        $b->append(')');

        $this->scope[0]->setType($assign, $o->returns);
        return;
      }
    }
    
    // First set type to void, emit assignment, then overwrite type with
    // right-hand-side's type. This is done in order to guard for checks
    // on uninitialized variables, which is OK during assignment.
    $this->scope[0]->setType($assign->variable, TypeName::$VOID);
    $this->emitOne($b, $assign->variable);
    $b->append($ops[$assign->op]);
    $this->emitOne($b, $assign->expression);
    $this->scope[0]->setType($assign->variable, $this->scope[0]->typeOf($assign->expression));
  }

  /**
   * Emit an operator
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.OperatorNode method
   */
  protected function emitOperator($b, $operator) {
    static $ovl= array(
      '~'   => 'concat',
      '-'   => 'minus',
      '+'   => 'plus',
      '*'   => 'times',
      '/'   => 'div',
      '%'   => 'mod',
    );
    
    $name= 'operator··'.$ovl[$operator->symbol];
    $this->enter(new MethodScope($name));
    $return= $this->resolveType($operator->returns);
    $this->metadata[0][1][$name]= array(
      DETAIL_ARGUMENTS    => array(),
      DETAIL_RETURNS      => $return->name(),
      DETAIL_THROWS       => array(),
      DETAIL_COMMENT      => $operator->comment
        ? trim(preg_replace('/\n\s+\* ?/', "\n", "\n ".substr($operator->comment, 4, strpos($operator->comment, '* @')- 2)))
        : null
      ,
      DETAIL_ANNOTATIONS  => array(),
      DETAIL_TARGET_ANNO  => array()
    );
    array_unshift($this->method, $name);
    $this->emitAnnotations($this->metadata[0][1][$name], (array)$operator->annotations);

    $b->append('public static function ')->append($name);
    $signature= $this->emitParameters($b, (array)$operator->parameters, '{');
    $this->emitAll($b, (array)$operator->body);
    $b->append('}');
    
    array_shift($this->method);
    $this->leave();
    
    // Register type information
    $o= new Operator();
    $o->symbol= $operator->symbol;
    $o->returns= new TypeName($return->name());
    $o->parameters= $signature;
    $o->modifiers= $operator->modifiers;
    $this->types[0]->addOperator($o);
  }

  /**
   * Emit method parameters
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   array<string, *>[] parameters
   * @param   string delim
   * @return  xp.compiler.TypeName[] the signature
   */
  protected function emitParameters($b, array $parameters, $delim) {
    $signature= array();
    $b->append('(');
    $s= sizeof($parameters)- 1;
    $defer= array();
    $usesGenerics= false;
    $genericParams= '';
    foreach ($parameters as $i => $param) {
      if (isset($param['assign'])) {
        if (null === ($field= $this->resolveType(new TypeName('self'))->getField($param['assign']))) {
          $this->error('F404', 'Method assignment parameter $this.'.$param['assign'].' references non-existant field');
          $t= TypeName::$VAR;
        } else {
          $t= $field->type;
        }
        $ptr= $this->resolveType($t);
        $param['name']= $param['assign'];
        $defer[]= '$this->'.$param['assign'].'= $'.$param['assign'].';';
      } else if (!$param['type']) {
        $t= TypeName::$VAR;
        $ptr= new TypeReference($t);
      } else {
        if (!$usesGenerics && $this->scope[0]->declarations[0]->name->isPlaceHolder($param['type'])) $usesGenerics= true;
        $t= $param['type'];
        $ptr= $this->resolveType($t);
        if (!$param['check'] || isset($param['vararg'])) {
          // No runtime type checks
        } else if ($t->isArray() || $t->isMap()) {
          $b->append('array ');
        } else if ($t->isClass() && !$this->scope[0]->declarations[0]->name->isPlaceHolder($t)) {
          $b->append($this->literal($ptr))->append(' ');
        } else if ('{' === $delim) {
          $defer[]= create(new Buffer('', $b->line))
            ->append('if (NULL !== $')->append($param['name'])->append(' && !is("'.$t->name.'", $')
            ->append($param['name'])
            ->append(')) throw new IllegalArgumentException("Argument ')
            ->append($i + 1)
            ->append(' passed to ".__METHOD__." must be of ')
            ->append($t->name)
            ->append(', ".'.$this->core.'::typeOf($')
            ->append($param['name'])
            ->append(')." given");')
          ;
        } else {
          // No checks in interfaces
        }
      }

      $signature[]= new TypeName($ptr->name());
      $genericParams.= ', '.$t->compoundName();
      $this->metadata[0][1][$this->method[0]][DETAIL_ARGUMENTS][$i]= $ptr->name();
      
      if (isset($param['vararg'])) {
        $genericParams.= '...';
        if ($i > 0) {
          $defer[]= '$'.$param['name'].'= array_slice(func_get_args(), '.$i.');';
        } else {
          $defer[]= '$'.$param['name'].'= func_get_args();';
        }
        $this->scope[0]->setType(new VariableNode($param['name']), new TypeName($t->name.'[]'));
        break;
      }
      
      $b->append('$'.$param['name']);
      if (isset($param['default'])) {
        $b->append('= ');
        $resolveable= false; 
        if ($param['default'] instanceof Resolveable) {
          try {
            $init= $param['default']->resolve();
            $b->append(var_export($init, true));
            $resolveable= true; 
          } catch (\lang\IllegalStateException $e) {
          }
        }
        if (!$resolveable) {
          $b->append('NULL');
          $init= new Buffer('', $b->line);
          $init->append('if (func_num_args() < ')->append($i + 1)->append(') { ');
          $init->append('$')->append($param['name'])->append('= ');
          $this->emitOne($init, $param['default']);
          $init->append('; }');
          $defer[]= $init;
        }
      }
      $i < $s && !isset($parameters[$i+ 1]['vararg']) && $b->append(',');
      
      $this->scope[0]->setType(new VariableNode($param['name']), $t);
    }
    $b->append(')');
    $b->append($delim);
    
    foreach ($defer as $src) {
      $b->append($src);
    }

    if ($usesGenerics) {
      $this->metadata[0][1][$this->method[0]][DETAIL_ANNOTATIONS]['generic']['params']= substr($genericParams, 2);
    }
    
    return $signature;
  }

  /**
   * Emit annotations
   *
   * @param   &var meta
   * @param   xp.compiler.ast.AnnotationNode[] annotations
   */
  protected function emitAnnotations(&$meta, $annotations) {
    foreach ($annotations as $annotation) {
      $this->emitAnnotation($meta, $annotation);
    }
  }

  /**
   * Emit annotation
   *
   * @param   &var meta
   * @param   xp.compiler.ast.AnnotationNode lambda
   */
  protected function emitAnnotation(&$meta, $annotation) {
    $params= array();
    foreach ((array)$annotation->parameters as $name => $value) {
      if ($value instanceof ClassAccessNode) {    // class literal
        $params[$name]= $this->resolveType($value->class)->name();
      } else if ($value instanceof Resolveable) {
        $params[$name]= $value->resolve();
      } else if ($value instanceof ArrayNode) {
        $params[$name]= array();
        foreach ($value->values as $element) {
          $element instanceof Resolveable && $params[$name][]= $element->resolve();
        }
      }
    }

    // Sort out where annotations should go
    if (isset($annotation->target)) {
      $ptr= &$meta[DETAIL_TARGET_ANNO][$annotation->target];
    } else {
      $ptr= &$meta[DETAIL_ANNOTATIONS];
    }

    // Set annotation value
    if (!$annotation->parameters) {
      $ptr[$annotation->type]= null;
    } else if (isset($annotation->parameters['default'])) {
      $ptr[$annotation->type]= $params['default'];
    } else {
      $ptr[$annotation->type]= $params;
    }
  }
  
  /**
   * Emit a lambda
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.LambdaNode lambda
   * @see     http://cr.openjdk.java.net/~mcimadamore/lambda_trans.pdf
   */
  protected function emitLambda($b, $lambda) {
    $unique= new TypeName('Lambda··'.strtr(uniqid('', true), '.', '·'));
    
    // Visit all statements, promoting local variable used within tp members
    $promoter= new LocalsToMemberPromoter();
    $parameters= $replaced= array();
    foreach ($lambda->parameters as $parameter) {
      $parameters[]= array('name' => $parameter->name, 'type' => TypeName::$VAR);
      $promoter->exclude($parameter->name);
    }
    $promoted= $promoter->promote($lambda);
    
    // Generate constructor
    $cparameters= $cstmt= $fields= array();
    foreach ($promoted['replaced'] as $name => $member) {
      $cparameters[]= array('name' => substr($name, 1), 'type' => TypeName::$VAR);
      $cstmt[]= new AssignmentNode(array(
        'variable'    => $member, 
        'expression'  => new VariableNode(substr($name, 1)), 
        'op'          => '='
      ));
      $fields[]= new FieldNode(array(
        'name'        => substr($name, 1), 
        'type'        => TypeName::$VAR)
      );
    }
    
    // Generate an anonymous lambda class
    $decl= new ClassNode(0, null, $unique, null, null, array_merge($fields, array(
      new ConstructorNode(array(
        'parameters' => $cparameters,
        'body'       => $cstmt
      )),
      new MethodNode(array(
        'name'        => 'invoke', 
        'parameters'  => $parameters,
        'body'        => $promoted['node']->statements,
        'returns'     => TypeName::$VAR
      ))
    )));
    $decl->synthetic= true;
    $this->scope[0]->declarations[]= $decl;
    
    // Finally emit array(new [UNIQUE]([CAPTURE]), "method")
    $b->append('array(new '.$unique->name.'('.implode(',', array_keys($promoted['replaced'])).'), \'invoke\')');
  }

  /**
   * Emit a method
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.MethodNode method
   */
  protected function emitMethod($b, $method) {
    if ($method->extension) {
      $this->scope[0]->addExtension(
        $type= $this->resolveType($method->extension),
        $this->resolveType(new TypeName('self'))->getMethod($method->name)
      );
      $this->metadata[0]['EXT'][$method->name]= $this->literal($type);   // HACK, this should be accessible in scope
    }
    $b->append(implode(' ', Modifiers::namesOf($method->modifiers)));
    $b->append(' function '.$method->name);
    
    // Begin
    $this->enter(new MethodScope($method->name));
    if (!Modifiers::isStatic($method->modifiers)) {
      $this->scope[0]->setType(new VariableNode('this'), $this->scope[0]->declarations[0]->name);
    }
    
    $return= $this->resolveType($method->returns, false);
    $this->metadata[0][1][$method->name]= array(
      DETAIL_ARGUMENTS    => array(),
      DETAIL_RETURNS      => $return->name(),
      DETAIL_THROWS       => array(),
      DETAIL_COMMENT      => $method->comment
        ? trim(preg_replace('/\n\s+\* ?/', "\n", "\n ".substr($method->comment, 4, strpos($method->comment, '* @')- 2)))
        : null
      ,
      DETAIL_ANNOTATIONS  => array(),
      DETAIL_TARGET_ANNO  => array()
    );
    array_unshift($this->method, $method->name);
    $this->emitAnnotations($this->metadata[0][1][$method->name], (array)$method->annotations);

    // Parameters, body
    if (null !== $method->body) {
      $signature= $this->emitParameters($b, (array)$method->parameters, '{');
      $this->emitAll($b, $method->body);
      $b->append('}');
    } else {
      $signature= $this->emitParameters($b, (array)$method->parameters, ';');
    }
    
    // Finalize
    if ($this->scope[0]->declarations[0]->name->isGeneric() && $this->scope[0]->declarations[0]->name->isPlaceholder($method->returns)) {
      $this->metadata[0][1][$method->name][DETAIL_ANNOTATIONS]['generic']['return']= $method->returns->compoundName();
    }

    array_shift($this->method);
    $this->leave();
    
    // Register type information
    $m= new Method();
    $m->name= $method->name;
    $m->returns= new TypeName($return->name());
    $m->parameters= $signature;
    $m->modifiers= $method->modifiers;
    $this->types[0]->addMethod($m, $method->extension);
  }

  /**
   * Emit static initializer
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticInitializerNode initializer
   */
  protected function emitStaticInitializer($b, $initializer) {
    $this->inits[0][2]= true;
    $b->append('static function __static() {');
    
    // Static initializations outside of initializer
    if ($this->inits[0][true]) {
      foreach ($this->inits[0][true] as $init) {
        $b->append($init);
      }
      unset($this->inits[0][true]);
    }
    $this->emitAll($b, (array)$initializer->statements);
    $b->append('}');
  }

  /**
   * Emit a constructor
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ConstructorNode constructor
   */
  protected function emitConstructor($b, $constructor) {
    $b->append(implode(' ', Modifiers::namesOf($constructor->modifiers)));
    $b->append(' function __construct');
    
    // Begin
    $this->enter(new MethodScope('__construct'));
    $this->scope[0]->setType(new VariableNode('this'), $this->scope[0]->declarations[0]->name);

    $this->metadata[0][1]['__construct']= array(
      DETAIL_ARGUMENTS    => array(),
      DETAIL_RETURNS      => null,
      DETAIL_THROWS       => array(),
      DETAIL_COMMENT      => preg_replace('/\n\s+\* ?/', "\n  ", "\n ".$constructor->comment),
      DETAIL_ANNOTATIONS  => array(),
      DETAIL_TARGET_ANNO  => array()
    );

    array_unshift($this->method, '__construct');
    $this->emitAnnotations($this->metadata[0][1]['__construct'], (array)$constructor->annotations);

    // Arguments, initializations, body
    if (null !== $constructor->body) {
      $signature= $this->emitParameters($b, (array)$constructor->parameters, '{');
      if ($this->inits[0][false]) {
        foreach ($this->inits[0][false] as $init) {
          $b->append($init);
        }
        unset($this->inits[0][false]);
      }
      $this->emitAll($b, $constructor->body);
      $b->append('}');
    } else {
      $signature= $this->emitParameters($b, (array)$constructor->parameters, ';');
    }
    
    // Finalize
    array_shift($this->method);
    $this->leave();

    // Register type information
    $c= new Constructor();
    $c->parameters= $signature;
    $c->modifiers= $constructor->modifiers;
    $this->types[0]->constructor= $c;
  }
  
  /**
   * Emit a class property
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.IndexerNode indexer
   */
  protected function emitIndexer($b, $indexer) {
    $params= array($indexer->parameter);
    $defines= array(
      'get'   => array('offsetGet', $params, $indexer->type),
      'set'   => array('offsetSet', array_merge($params, array(array('name' => 'value', 'type' => $indexer->type, 'check' => false))), TypeName::$VOID),
      'isset' => array('offsetExists', $params, new TypeName('bool')),
      'unset' => array('offsetUnset', $params, TypeName::$VOID),
    );

    foreach ($indexer->handlers as $name => $statements) {
      $this->emitOne($b, new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> null,
        'name'       => $defines[$name][0],
        'returns'    => $defines[$name][2],
        'parameters' => $defines[$name][1],
        'throws'     => null,
        'body'       => $statements,
        'comment'      => '(Generated)'
      )));
    }
    
    // Register type information
    $i= new Indexer();
    $i->type= new TypeName($this->resolveType($indexer->type)->name());
    $i->parameter= new TypeName($this->resolveType($indexer->parameter['type'])->name());
    $i->modifiers= $indexer->modifiers;
    $this->types[0]->indexer= $i;
  }

  /**
   * Emits class registration
   *
   * <code>
   *   xp::$cn['class.'.$name]= $qualified;
   *   xp::$meta['details.'.$qualified]= $meta;
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.TypeDeclarationNode
   * @param   string qualified
   */
  protected function registerClass($b, $declaration, $qualified) {
    unset($this->metadata[0]['EXT']);

    // Retain comment
    $this->metadata[0]['class'][DETAIL_COMMENT]= $declaration->comment
      ? trim(preg_replace('/\n\s+\* ?/', "\n", "\n ".substr($declaration->comment, 4, strpos($declaration->comment, '* @')- 2)))
      : null
    ;

    // Copy annotations
    $this->emitAnnotations($this->metadata[0]['class'], (array)$declaration->annotations);

    $b->append($this->core.'::$cn[\''.$declaration->literal.'\']= \''.$qualified.'\';');
    $b->append($this->core.'::$meta[\''.$qualified.'\']= '.var_export($this->metadata[0], true).';');

    // Run static initializer if existant on synthetic types
    if ($declaration->synthetic && $this->inits[0][2]) {
      $b->append($declaration->literal)->append('::__static();');
    }
  }

  /**
   * Emit a class property
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.PropertyNode property
   */
  protected function emitProperty($b, $property) {
    foreach ($property->handlers as $name => $statements) {
      $this->properties[0][$name][$property->name]= array($property->type, $statements);
    }

    // Register type information
    $p= new Property();
    $p->name= $property->name;
    $p->type= new TypeName($this->resolveType($property->type)->name());
    $p->modifiers= $property->modifiers;
    $this->types[0]->addProperty($p);
  }    

  /**
   * Emit class properties.
   *
   * Creates the equivalent of the following: 
   * <code>
   *   public function __get($name) {
   *     if ('length' === $name) {
   *       return $this->_length;
   *     } else if ('chars' === $name) {
   *       return str_split($this->buffer);
   *     }
   *   }
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   array<string, array<string, xp.compiler.ast.Node[]>> properties
   */
  protected function emitProperties($b, array $properties) {
    static $mangled= '··name';
    
    $auto= array();
    if (!empty($properties['get'])) {
      $b->append('function __get($'.$mangled.') {');
      $this->enter(new MethodScope('__get'));
      $this->scope[0]->setType(new VariableNode('this'), $this->scope[0]->declarations[0]->name);
      foreach ($properties['get'] as $name => $definition) {
        $b->append('if (\''.$name.'\' === $'.$mangled.') {');
        if (null === $definition[1]) {
          $b->append('return $this->__·'.$name.';');
          $auto[$name]= true;
        } else {
          $this->emitAll($b, $definition[1]);
        }
        $b->append('} else ');
      }
      $b->append('return parent::__get($'.$mangled.'); }');
      $this->leave();
    }
    if (!empty($properties['set'])) {
      $b->append('function __set($'.$mangled.', $value) {');
      $this->enter(new MethodScope('__set'));
      $this->scope[0]->setType(new VariableNode('this'), $this->scope[0]->declarations[0]->name);
      foreach ($properties['set'] as $name => $definition) {
        $this->scope[0]->setType(new VariableNode('value'), $definition[0]);
        $b->append('if (\''.$name.'\' === $'.$mangled.') {');
        if (null === $definition[1]) {
          $b->append('$this->__·'.$name.'= $value;');
          $auto[$name]= true;
        } else {
          $this->emitAll($b, $definition[1]);
        }
        $b->append('} else ');
      }
      $b->append('parent::__set($'.$mangled.', $value); }');
      $this->leave();
    }
    
    // Declare auto-properties as private with null as initial value
    foreach ($auto as $name => $none) $b->append('private $__·'.$name.'= null;');
  }

  /**
   * Emit an enum member
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.EnumMemberNode member
   */
  protected function emitEnumMember($b, $member) {
    $b->append('public static $'.$member->name.';');

    // Add field metadata (type, stored in @type annotation, see
    // lang.reflect.Field and lang.XPClass::detailsForField())
    $type= $this->resolveType(new TypeName('self'));
    $this->metadata[0][0][$member->name]= array(
      DETAIL_ANNOTATIONS  => array('type' => $type->name())
    );

    // Register type information
    $f= new Field();
    $f->name= $member->name;
    $f->type= new TypeName($type->name());
    $f->modifiers= MODIFIER_PUBLIC | MODIFIER_STATIC;
    $this->types[0]->addField($f);
  }  

  /**
   * Emit a class constant
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ClassConstantNode const
   */
  protected function emitClassConstant($b, $const) {    
    $b->append('const ')->append($const->name)->append('=');
    $this->emitOne($b, $const->value);
    $b->append(';');
    
    // Register type information. 
    $c= new Constant();
    $c->type= new TypeName($this->resolveType($const->type)->name());
    $c->name= $const->name;
    $c->value= $const->value instanceof Resolveable ? $const->value->resolve() : $const->value;
    $this->types[0]->addConstant($c);
  }
  
  /**
   * Emit a class field
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.FieldNode field
   */
  protected function emitField($b, $field) {    
    $static= Modifiers::isStatic($field->modifiers);
    
    // See whether an initialization is necessary
    $initializable= false;
    if ($field->initialization) {
      if ($field->initialization instanceof Resolveable) {
        try {
          $init= $field->initialization->resolve();
          $initializable= true;
        } catch (\lang\IllegalStateException $e) {
          $this->warn('R100', $e->getMessage(), $field->initialization);
          $initializable= false;
        }
      }

      if (!$initializable) {
        $init= new Buffer('', $b->line);
        $this->enter(new MethodScope('<init>'));
        if ($static) {
          $variable= new StaticMemberAccessNode(new TypeName('self'), $field->name);
        } else {
          $variable= new MemberAccessNode(new VariableNode('this'), $field->name);
          $this->scope[0]->setType(new VariableNode('this'), $this->scope[0]->declarations[0]->name);
        }
        $this->emitOne($init, new AssignmentNode(array(
          'variable'   => $variable,
          'expression' => $field->initialization,
          'op'         => '=',
        )));
        $init->append(';');
        $type= $this->scope[0]->typeOf($variable);
        $this->leave();
        $this->inits[0][$static][]= $init;
        $this->scope[0]->setType($field->initialization, $type);
      }

      // If the field is "var" and we have an initialization, determine
      // the type from there
      if ($field->type->isVariable()) {
        $field->type= $this->scope[0]->typeOf($field->initialization);
      }
    }

    switch ($field->modifiers & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
      case MODIFIER_PRIVATE: $b->append('private '); break;
      case MODIFIER_PROTECTED: $b->append('protected '); break;
      default: $b->append('public '); break;
    }
    $static && $b->append('static ');
    $b->append('$'.$field->name);
    $initializable && $b->append('= ')->append(var_export($init, true));
    $b->append(';');

    // Copy annotations
    $this->metadata[0][0][$field->name]= array(DETAIL_ANNOTATIONS => array());
    $this->emitAnnotations($this->metadata[0][0][$field->name], (array)$field->annotations);

    // Add field metadata (type, stored in @type annotation, see
    // lang.reflect.Field and lang.XPClass::detailsForField()). 
    $type= $this->resolveType($field->type);
    $this->metadata[0][0][$field->name][DETAIL_ANNOTATIONS]['type']= $type->name();

    // Register type information
    $f= new Field();
    $f->name= $field->name;
    $f->type= new TypeName($type->name());
    $f->modifiers= $field->modifiers;
    $this->types[0]->addField($f);
  }

  /**
   * Emit a class declaration
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ClassNode declaration
   */
  protected function emitClass($b, $declaration) {
    $parent= $declaration->parent ?: new TypeName('lang.Object');
    $parentType= $this->resolveType($parent);
    $thisType= new TypeDeclaration(new ParseTree($this->scope[0]->package, array(), $declaration), $parentType);
    $this->scope[0]->addResolved('self', $thisType);
    $this->scope[0]->addResolved('parent', $parentType);
    
    $this->enter(new TypeDeclarationScope());    
    $this->emitTypeName($b, 'class', $declaration);
    $b->append(' extends '.$this->literal($parentType, true));
    array_unshift($this->metadata, array(array(), array()));
    $this->metadata[0]['class'][DETAIL_ANNOTATIONS]= array();
    array_unshift($this->properties, array());
    array_unshift($this->inits, array(false => array(), true => array(), 2 => false));

    // Generics
    if ($declaration->name->isGeneric()) {
      $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['self']= $this->genericComponentAsMetadata($declaration->name);
    }
    if ($parent->isGeneric()) {
      $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['parent']= $this->genericComponentAsMetadata($parent);
    }

    // Check if we need to implement ArrayAccess
    foreach ((array)$declaration->body as $node) {
      if ($node instanceof IndexerNode) {
        $declaration->implements[]= 'ArrayAccess';
      }
    }
    
    // Interfaces
    if ($declaration->implements) {
      $b->append(' implements ');
      $s= sizeof($declaration->implements)- 1;
      foreach ($declaration->implements as $i => $type) {
        if ($type instanceof TypeName) {
          if ($type->isGeneric()) {
            $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['implements'][$i]= $this->genericComponentAsMetadata($type);
          }
          $b->append($this->literal($this->resolveType($type), true));
        } else {
          $b->append($type);
        }
        $i < $s && $b->append(', ');
      }
    }
    
    // Members
    $b->append('{');
    foreach ((array)$declaration->body as $node) {
      $this->emitOne($b, $node);
    }
    $this->emitProperties($b, $this->properties[0]);
    
    // Generate a constructor if initializations are available.
    // They will have already been emitted if a constructor exists!
    if ($this->inits[0][false]) {
      $arguments= array();
      $parameters= array();
      if ($parentType->hasConstructor()) {
        foreach ($parentType->getConstructor()->parameters as $i => $type) {
          $parameters[]= array('name' => '··a'.$i, 'type' => $type);    // TODO: default
          $arguments[]= new VariableNode('··a'.$i);
        }
        $body= array(new StaticMethodCallNode(new TypeName('parent'), '__construct', $arguments));
      } else {
        $body= array();
      }
      $this->emitOne($b, new ConstructorNode(array(
        'modifiers'    => MODIFIER_PUBLIC,
        'parameters'   => $parameters,
        'annotations'  => null,
        'body'         => $body,
        'comment'      => '(Generated)',
        'position'     => $declaration->position
      )));
    }

    // Generate a static initializer if initializations are available.
    // They will have already been emitted if a static initializer exists!
    if ($this->inits[0][true]) {
      $this->emitOne($b, new StaticInitializerNode(null));
    }
    
    // Create __import.
    if (isset($this->metadata[0]['EXT'])) {
      $b->append('static function __import($scope) {');
      foreach ($this->metadata[0]['EXT'] as $method => $type) {
        $b->append($this->core.'::$ext[$scope][\'')->append($type)->append('\']= \'')->append($thisType->literal())->append('\';');
      }
      $b->append('}');
    }

    // Generic instances have {definition-type, null, [argument-type[0..n]]} 
    // stored  as type names in their details
    if (isset($declaration->generic)) {
      $this->metadata[0]['class'][DETAIL_GENERIC]= $declaration->generic;
    }

    $b->append('}');
    $this->leave();
    $this->registerClass($b, $declaration, $thisType->name());
    array_shift($this->properties);
    array_shift($this->metadata);
    array_shift($this->inits);

    // Register type info
    $this->types[0]->name= $thisType->name();
    $this->types[0]->kind= Types::CLASS_KIND;
    $this->types[0]->literal= $declaration->literal;
    $this->types[0]->parent= $parentType;
  }

  /**
   * Emit an enum declaration
   *
   * Basic form:
   * <code>
   *   public enum Day { MON, TUE, WED, THU, FRI, SAT, SUN }
   * </code>
   *
   * With values:
   * <code>
   *   public enum Coin { penny(1), nickel(2), dime(10), quarter(25) }
   * </code>
   *
   * Abstract:
   * <code>
   *   public abstract enum Operation {
   *     plus {
   *       public int evaluate(int $x, int $y) { return $x + $y; }
   *     },
   *     minus {
   *       public int evaluate(int $x, int $y) { return $x - $y; }
   *     };
   *
   *     public abstract int evaluate(int $x, int $y);
   *   }
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.EnumNode declaration
   */
  protected function emitEnum($b, $declaration) {
    $parent= $declaration->parent ?: new TypeName('lang.Enum');
    $parentType= $this->resolveType($parent);
    $thisType= new TypeDeclaration(new ParseTree($this->scope[0]->package, array(), $declaration), $parentType);
    $this->scope[0]->addResolved('self', $thisType);
    $this->scope[0]->addResolved('parent', $parentType);
    
    // FIXME: ???
    $this->scope[0]->addResolved($declaration->name->name, $thisType);
    $this->scope[0]->imports[$declaration->name->name]= $declaration->name->name;

    $this->enter(new TypeDeclarationScope());

    // Ensure parent class and interfaces are loaded
    $this->emitTypeName($b, 'class', $declaration);
    $b->append(' extends '.$parentType->literal(true));
    array_unshift($this->metadata, array(array(), array()));
    $this->metadata[0]['class'][DETAIL_ANNOTATIONS]= array();
    array_unshift($this->properties, array('get' => array(), 'set' => array()));
    $abstract= Modifiers::isAbstract($declaration->modifiers);

    // Generics
    if ($declaration->name->isGeneric()) {
      $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['self']= $this->genericComponentAsMetadata($declaration->name);
    }
    if ($parent->isGeneric()) {
      $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['parent']= $this->genericComponentAsMetadata($parent);
    }

    // Interfaces
    if ($declaration->implements) {
      $b->append(' implements ');
      $s= sizeof($declaration->implements)- 1;
      foreach ($declaration->implements as $i => $type) {
        if ($type->isGeneric()) {
          $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['implements'][$i]= $this->genericComponentAsMetadata($type);
        }
        $b->append($this->resolveType($type)->literal(true));
        $i < $s && $b->append(', ');
      }
    }
    
    // Member declaration
    $b->append(' {');
    
    // public static self[] values() { return parent::membersOf(__CLASS__) }
    $declaration->body[]= new MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'name'       => 'values',
      'returns'    => new TypeName('self[]'),
      'parameters' => null,
      'throws'     => null,
      'body'       => array(
        new ReturnNode(new StaticMethodCallNode(
          new TypeName('parent'),
          'membersOf', 
          array(new StringNode($this->literal($thisType)))
        ))
      ),
      'comment'    => '(Generated)'
    ));

    // Members
    foreach ((array)$declaration->body as $node) {
      $this->emitOne($b, $node);
    }
    $this->emitProperties($b, $this->properties[0]);
    
    // Initialization
    $b->append('static function __static() {');
    foreach ($declaration->body as $i => $member) {
      if (!$member instanceof EnumMemberNode) continue;
      $b->append('self::$'.$member->name.'= ');
      if ($member->body) {
        if (!$abstract) {
          $this->error('E403', 'Only abstract enums can contain members with bodies ('.$member->name.')');
          // Continues so declaration is closed
        }
        
        $unique= new TypeName($declaration->name->name.'··'.$member->name);
        $decl= new ClassNode(0, null, $unique, $declaration->name, array(), $member->body);
        $decl->synthetic= true;
        $ptr= new TypeDeclaration(new ParseTree(null, array(), $decl), $thisType);
        $this->scope[0]->declarations[]= $decl;
        $b->append('new '.$unique->name.'(');
      } else {
        $b->append('new self(');
      }
      if ($member->value) {
        $this->emitOne($b, $member->value);
      } else {
        $b->append($i);
      }
      $b->append(', \''.$member->name.'\');');
    }
    $b->append('}');

    // Finish
    $b->append('}');

    $this->leave();
    $this->registerClass($b, $declaration, $thisType->name());
    array_shift($this->properties);
    array_shift($this->metadata);

    // Register type info
    $this->types[0]->name= $thisType->name();
    $this->types[0]->kind= Types::ENUM_KIND;
    $this->types[0]->literal= $declaration->literal;
    $this->types[0]->parent= $parentType;
  }

  /**
   * Emit a Interface declaration
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InterfaceNode declaration
   */
  protected function emitInterface($b, $declaration) {
    $thisType= new TypeDeclaration(new ParseTree($this->scope[0]->package, array(), $declaration));
    $this->scope[0]->addResolved('self', $thisType);

    $this->enter(new TypeDeclarationScope());    
    $this->emitTypeName($b, 'interface', $declaration);
    array_unshift($this->metadata, array(array(), array()));
    $this->metadata[0]['class'][DETAIL_ANNOTATIONS]= array();

    // Generics
    if ($declaration->name->isGeneric()) {
      $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['self']= $this->genericComponentAsMetadata($declaration->name);
    }

    if ($declaration->parents) {
      $b->append(' extends ');
      $s= sizeof($declaration->parents)- 1;
      foreach ((array)$declaration->parents as $i => $type) {
        if ($type->isGeneric()) {
          $this->metadata[0]['class'][DETAIL_ANNOTATIONS]['generic']['extends'][$i]= $this->genericComponentAsMetadata($type);
        }
        $b->append($this->resolveType($type)->literal(true));
        $i < $s && $b->append(', ');
      }
    }
    $b->append(' {');
    foreach ((array)$declaration->body as $node) {
      $this->emitOne($b, $node);
    }
    $b->append('}');

    $this->leave();
    $this->registerClass($b, $declaration, $thisType->name());
    array_shift($this->metadata);

    // Register type info
    $this->types[0]->name= $thisType->name();
    $this->types[0]->kind= Types::INTERFACE_KIND;
    $this->types[0]->literal= $declaration->literal;
    $this->types[0]->parent= null;
  }

  /**
   * Emit dynamic instanceof
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.DynamicInstanceOfNode instanceof
   */
  protected function emitDynamicInstanceOf($b, $instanceof) {
    $this->emitOne($b, $instanceof->expression);
    $b->append(' instanceof ')->append('$')->append($instanceof->variable);
  }

  /**
   * Emit instanceof
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.InstanceOfNode instanceof
   */
  protected function emitInstanceOf($b, $instanceof) {
    $this->emitOne($b, $instanceof->expression);
    $b->append(' instanceof ')->append($this->resolveType($instanceof->type)->literal());
  }

  /**
   * Emit clone
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.CloneNode clone
   */
  protected function emitClone($b, $clone) {
    $b->append('clone ');
    $this->emitOne($b, $clone->expression);
  }

  /**
   * Emit import
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ImportNode import
   */
  protected function emitImport($b, $import) {
    if ('.*' == substr($import->name, -2)) {
      $this->scope[0]->addPackageImport(substr($import->name, 0, -2));
    } else {
      $this->scope[0]->addTypeImport($import->name);
    }
  }

  /**
   * Emit native import
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.NativeImportNode import
   */
  protected function emitNativeImport($b, $import) {
    $imported= $this->scope[0]->importer->import($import->name);
    if (0 === ($k= key($imported))) {
      $this->scope[0]->statics[0]= array_merge($this->scope[0]->statics[0], $imported[$k]);
    } else {
      $this->scope[0]->statics[$k]= $imported[$k];
    }
  }
  
  /**
   * Emit static import
   *
   * Given the following:
   * <code>
   *   import static rdbms.criterion.Restrictions.*;
   * </code>
   *
   * A call to lessThanOrEqualTo() "function" then resolves to a static
   * method call to Restrictions::lessThanOrEqualTo()
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.StaticImportNode import
   */
  protected function emitStaticImport($b, $import) {
    if ('.*' == substr($import->name, -2)) {
      $this->scope[0]->statics[0][substr($import->name, 0, -2)]= $this->resolveType(new TypeName(substr($import->name, 0, -2)));
    } else {
      $p= strrpos($import->name, '.');
      $method= $this->resolveType(new TypeName(substr($import->name, 0, $p)))->getMethod(substr($import->name, $p+ 1));
      $this->scope[0]->statics[$method->name()]= $method;
    }
  }

  /**
   * Emit a return statement
   * <code>
   *   return;                // void return
   *   return [EXPRESSION];   // returning a value
   * </code>
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.ReturnNode new
   */
  protected function emitReturn($b, $return) {
    $this->finalizers[0] && $this->emitOne($b, $this->finalizers[0]);
    
    if (!$return->expression) {
      $b->append('return');
    } else {
      $b->append('return ');
      $this->emitOne($b, $return->expression);
    }
  }

  /**
   * Emit a silence node
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.SilenceOperatorNode silenced
   */
  protected function emitSilenceOperator($b, $silenced) {
    $b->append('@');
    $this->emitOne($b, $silenced->expression);
    $this->scope[0]->setType($silenced, $this->scope[0]->typeOf($silenced->expression));
  }

  /**
   * Emit all given nodes
   *
   * @param   xp.compiler.emit.Buffer b
   * @param   xp.compiler.ast.Node[] nodes
   */
  protected function emitAll($b, array $nodes) {
    foreach ($nodes as $node) {
      $this->emitOne($b, $node);
      $b->append(';');
    }
  }

  /**
   * Resolve a type, raising an error message if type resolution
   * raises an error and return an unknown type reference in this
   * case.
   *
   * @param   xp.compiler.types.TypeName
   * @param   bool register default true
   * @return  xp.compiler.types.Types
   */
  protected function resolveType(TypeName $t, $register= true) {
    try {
      $ptr= $this->scope[0]->resolveType($t, $register);
      $this->cat && $this->cat->info('Resolve', $t, '=', $ptr);
      return $ptr;
    } catch (ResolveException $e) {
      $this->cat && $this->cat->warn('Resolve', $t, '~', $e->compoundMessage());
      $this->error('R'.$e->getKind(), $e->compoundMessage());
      return new TypeReference($t, Types::UNKNOWN_KIND);
    }
  }

  /**
   * Entry point
   *
   * @param   xp.compiler.ast.ParseTree tree
   * @param   xp.compiler.types.Scope scope
   * @return  xp.compiler.Result
   */
  public function emit(ParseTree $tree, Scope $scope) {
    $bytes= new Buffer('', 1);
    
    array_unshift($this->local, array());
    array_unshift($this->temp, 0);
    array_unshift($this->scope, $scope->enter(new CompilationUnitScope()));
    $this->scope[0]->importer= new NativeImporter();
    $this->scope[0]->declarations= array($tree->declaration);
    $this->scope[0]->package= $tree->package;
    
    // Functions from lang.base.php
    $this->scope[0]->statics= array(
      0             => array(),
      'newinstance' => true,
      'with'        => true,
      'create'      => true,
      'raise'       => true,
      'delete'      => true,
      'cast'        => true,
      'is'          => true,
      'this'        => true,
      'isset'       => true,
      'unset'       => true,
      'empty'       => true,
      'eval'        => true,
      'include'     => true,
      'require'     => true,
      'include_once'=> true,
      'require_once'=> true,
    );

    $this->cat && $this->cat->infof('== Enter %s ==', basename($tree->origin));

    // Import and declarations
    $t= null;
    $this->scope[0]->addResolved('self', new TypeDeclaration($tree));   // FIXME: for import self
    $this->emitAll($bytes, (array)$tree->imports);
    while ($this->scope[0]->declarations) {
      array_unshift($this->types, new CompiledType());

      $decl= current($this->scope[0]->declarations);
      $this->local[0][$decl->name->name]= true;
      $this->emitOne($bytes, $decl);
      array_shift($this->scope[0]->declarations);

      $t || $t= $this->types[0];
    }

    // Load used classes
    $this->emitUses($bytes, $this->scope[0]->used);

    // Leave scope
    array_shift($this->local);
    $this->leave();
    
    // Check on errors
    $this->cat && $this->cat->infof(
      '== %s: %d error(s), %d warning(s) ==', 
      basename($tree->origin), 
      sizeof($this->messages['errors']), 
      sizeof($this->messages['warnings'])
    );
    if ($this->messages['errors']) {
      throw new \lang\FormatException('Errors emitting '.$tree->origin.': '.\xp::stringOf($this->messages));
    }

    // Finalize
    return new Result($t, $bytes);
  }    
}
