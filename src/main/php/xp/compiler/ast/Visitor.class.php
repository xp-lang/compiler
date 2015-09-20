<?php namespace xp\compiler\ast;

/**
 * Visits a parse tree
 *
 * @test    xp://net.xp_lang.tests.VisitorTest
 */
abstract class Visitor extends \lang\Object {

  /**
   * Visit an annotation
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitAnnotation(AnnotationNode $node) {
    $node->parameters= $this->visitAll($node->parameters);
    return $node;
  }

  /**
   * Visit an ARM block
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitArm(ArmNode $node) {
    $node->initializations= $this->visitAll($node->initializations);
    $node->variables= $this->visitAll($node->variables);
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit an array access
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitArrayAccess(ArrayAccessNode $node) {
    $node->target= $this->visitOne($node->target);
    $node->offset && $node->offset= $this->visitOne($node->offset);
    return $node;
  }

  /**
   * Visit an array
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitArray(ArrayNode $node) {
    $node->values= $this->visitAll($node->values);
    return $node;
  }

  /**
   * Visit an assignment
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitAssignment(AssignmentNode $node) {
    $node->variable= $this->visitOne($node->variable);
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit a binary op
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitBinaryOp(BinaryOpNode $node) {
    $node->lhs= $this->visitOne($node->lhs);
    $node->rhs= $this->visitOne($node->rhs);
    return $node;
  }

  /**
   * Visit an boolean
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitBoolean(BooleanNode $node) {
    return $node;
  }

  /**
   * Visit a boolean operation
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitBooleanOp(BooleanOpNode $node) {
    $node->lhs= $this->visitOne($node->lhs);
    $node->rhs= $this->visitOne($node->rhs);
    return $node;
  }

  /**
   * Visit an break statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitBreak(BreakNode $node) {
    return $node;
  }

  /**
   * Visit an case statement (inside a switch)
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitCase(CaseNode $node) {
    $node->expression= $this->visitOne($node->expression);
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit a cast expression
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitCast(CastNode $node) {
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit catch
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitCatch(CatchNode $node) {
    $node->variable= $this->visitOne(new VariableNode($node->variable))->name;
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit member access
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitMemberAccess(MemberAccessNode $node) {
    $node->target= $this->visitOne($node->target);
    return $node;
  }

  /**
   * Visit method call
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitMethodCall(MethodCallNode $node) {
    $node->target= $this->visitOne($node->target);
    $node->arguments && $node->arguments= $this->visitAll($node->arguments);
    return $node;
  }

  /**
   * Visit instance call
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitInstanceCall(InstanceCallNode $node) {
    $node->target= $this->visitOne($node->target);
    $node->arguments && $node->arguments= $this->visitAll($node->arguments);
    return $node;
  }

  /**
   * Visit static member access
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitStaticMemberAccess(StaticMemberAccessNode $node) {
    return $node;
  }

  /**
   * Visit static method call
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitStaticMethodCall(StaticMethodCallNode $node) {
    $node->arguments && $node->arguments= $this->visitAll($node->arguments);
    return $node;
  }

  /**
   * Visit constant access
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitConstantAccess(ConstantAccessNode $node) {
    return $node;
  }

  /**
   * Visit class access
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitClassAccess(ClassAccessNode $node) {
    return $node;
  }

  /**
   * Visit class constants
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitClassConstant(ClassConstantNode $node) {
    $node->value= $this->visitOne($node->value);
    return $node;
  }

  /**
   * Visit a class declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitClass(ClassNode $node) {
    $this->visitAll((array)$node->body);
    return $node;
  }

  /**
   * Visit a clone expression
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitClone(CloneNode $node) {
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit a comparison
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitComparison(ComparisonNode $node) {
    $node->lhs= $this->visitOne($node->lhs);
    $node->rhs= $this->visitOne($node->rhs);
    return $node;
  }

  /**
   * Visit a constant literal
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitConstant(ConstantNode $node) {
    return $node;
  }

  /**
   * Visit a constructor
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitConstructor(ConstructorNode $node) {
    $this->visitAll((array)$node->body);
    return $node;
  }

  /**
   * Visit a continue statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitContinue(ContinueNode $node) {
    return $node;
  }

  /**
   * Visit a decimal literal
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitDecimal(DecimalNode $node) {
    return $node;
  }

  /**
   * Visit default (inside switch)
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitDefault(DefaultNode $node) {
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit a do ... while loop
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitDo(DoNode $node) {
    $node->statements= $this->visitAll($node->statements);
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit an else block
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitElse(ElseNode $node) {
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit an enum member
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitEnumMember(EnumMemberNode $node) {
    $node->body && $node->body= $this->visitAll($node->body);
    return $node;
  }

  /**
   * Visit an enum declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitEnum(EnumNode $node) {
    $node->body= $this->visitAll($node->body);
    return $node;
  }

  /**
   * Visit a field declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitField(FieldNode $node) {
    $node->initialization && $this->visitOne($node->initialization);
    return $node;
  }

  /**
   * Visit a finally block
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitFinally(FinallyNode $node) {
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit a for statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitFor(ForNode $node) {
    $node->initialization= $this->visitAll($node->initialization);
    $node->condition= $this->visitAll($node->condition);
    $node->loop= $this->visitAll($node->loop);
    $node->statements && $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit an annotation
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitForeach(ForeachNode $node) {
    $this->visitOne($node->expression);
    if (isset($node->assignment['key'])) {
      $node->assignment['key']= $this->visitOne(new VariableNode($node->assignment['key']))->name;
    }
    $node->assignment['value']= $this->visitOne(new VariableNode($node->assignment['value']))->name;
    $node->statements && $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit a hex literal
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitHex(HexNode $node) {
    return $node;
  }

  /**
   * Visit an octal literal
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitOctal(OctalNode $node) {
    return $node;
  }

  /**
   * Visit an annotation
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitIf(IfNode $node) {
    $node->condition= $this->visitOne($node->condition);
    $node->statements && $node->statements= $this->visitAll($node->statements);
    $node->otherwise && $node->otherwise= $this->visitOne($node->otherwise);
    return $node;
  }

  /**
   * Visit an import
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitImport(ImportNode $node) {
    return $node;
  }

  /**
   * Visit an indexer declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitIndexer(IndexerNode $node) {
    foreach ($node->handlers as $name => $statements) {
      $statements && $node->handlers[$name]= $this->visitAll($statements);
    }
    return $node;
  }

  /**
   * Visit an instance creation
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitInstanceCreation(InstanceCreationNode $node) {
    $node->parameters= $this->visitAll((array)$node->parameters);
    $node->body && $node->body= $this->visitAll((array)$node->body);
    return $node;
  }

  /**
   * Visit an instanceof statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitInstanceOf(InstanceOfNode $node) {
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit an integer literal
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitInteger(IntegerNode $node) {
    return $node;
  }

  /**
   * Visit an interface declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitInterface(InterfaceNode $node) {
    $node->body= $this->visitAll((array)$node->body);
    return $node;
  }

  /**
   * Visit an invocation
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitInvocation(InvocationNode $node) {
    $node->arguments= $this->visitAll((array)$node->arguments);
    return $node;
  }

  /**
   * Visit a lambda
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitLambda(LambdaNode $node) {
    $node->statements= $this->visitAll((array)$node->statements);
    return $node;
  }

  /**
   * Visit a map declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitMap(MapNode $node) {
    if ($node->elements) foreach ($node->elements as $i => $pair) {
      $node->elements[$i]= $this->visitAll($pair);
    }
    return $node;
  }

  /**
   * Visit a map declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitMethod(MethodNode $node) {
    $node->body= $this->visitAll($node->body);
    return $node;
  }

  /**
   * Visit a native import
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitNativeImport(NativeImportNode $node) {
    return $node;
  }

  /**
   * Visit a NOOP
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitNoop(NoopNode $node) {
    return $node;
  }

  /**
   * Visit a null literal
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitNull(NullNode $node) {
    return $node;
  }

  /**
   * Visit an operator
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitOperator(OperatorNode $node) {
    $node->body= $this->visitAll($node->body);
    return $node;
  }

  /**
   * Visit a package declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitPackage(PackageNode $node) {
    return $node;
  }

  /**
   * Visit a property
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitProperty(PropertyNode $node) {
    foreach ($node->handlers as $name => $statements) {
      $statements && $node->handlers[$name]= $this->visitAll($statements);
    }
    return $node;
  }

  /**
   * Visit a map declaration
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitReturn(ReturnNode $node) {
    $node->expression && $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit a statements list
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitStatements(StatementsNode $node) {
    $node->list= $this->visitAll($node->list);
    return $node;
  }

  /**
   * Visit a static import
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitStaticImport(StaticImportNode $node) {
    return $node;
  }

  /**
   * Visit a static initializer
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitStaticInitializer(StaticInitializerNode $node) {
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit a string literal
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitString(StringNode $node) {
    return $node;
  }

  /**
   * Visit a switch statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitSwitch(SwitchNode $node) {
    $node->expression= $this->visitOne($node->expression);
    $node->cases= $this->visitAll($node->cases);
    return $node;
  }

  /**
   * Visit a ternary operator
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitTernary(TernaryNode $node) {
    $node->condition= $this->visitOne($node->condition);
    $node->expression && $node->expression= $this->visitOne($node->expression);
    $node->conditional= $this->visitOne($node->conditional);
    return $node;
  }

  /**
   * Visit a throw statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitThrow(ThrowNode $node) {
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit a try statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitTry(TryNode $node) {
    $node->statements= $this->visitAll($node->statements);
    $node->handling= $this->visitAll($node->handling);
    return $node;
  }

  /**
   * Visit a unary operator
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitUnaryOp(UnaryOpNode $node) {
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit a variable
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitVariable(VariableNode $node) {
    return $node;
  }

  /**
   * Visit a while loop
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitWhile(WhileNode $node) {
    $node->expression= $this->visitOne($node->expression);
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit a with statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitWith(WithNode $node) {
    $node->assignments= $this->visitAll($node->assignments);
    $node->statements= $this->visitAll($node->statements);
    return $node;
  }

  /**
   * Visit a () statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitBracedExpression(BracedExpressionNode $node) {
    $node->expression= $this->visitOne($node->expression);
    return $node;
  }

  /**
   * Visit a yield statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitYield(YieldNode $node) {
    $node->key && $node->key= $this->visitOne($node->key);
    $node->value && $node->value= $this->visitOne($node->value);
    return $node;
  }

  /**
   * Visit a yield statement
   *
   * @param   xp.compiler.ast.Node node
   */
  protected function visitYieldFrom(YieldFromNode $node) {
    $node->expr= $this->visitOne($node->expr);
    return $node;
  }

  /**
   * Visit a node. Delegates to visit*() methods
   *
   * @param   xp.compiler.ast.Node node
   */
  public function visitOne(Node $node) {
    $target= 'visit'.substr(get_class($node), strlen('xp\\compiler\\ast\\'), -strlen('Node'));
    if (!method_exists($this, $target)) {
      throw new \lang\IllegalArgumentException('Don\'t know how to visit '.$node->getClassName().'s');
    }
    return call_user_func(array($this, $target), $node);
  }

  /**
   * Visit an array of nodes. Delegates to visit*() methods
   *
   * @param   xp.compiler.ast.Node[] nodes
   */
  public function visitAll(array $nodes) {
    $r= array();
    foreach ($nodes as $node) {
      $r[]= $this->visitOne($node);
    }
    return $r;
  }
}
