<?php namespace net\xp_lang\tests;

use xp\compiler\ast\Visitor;
use xp\compiler\types\TypeName;
use xp\compiler\ast\Node;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\StringNode;
use xp\compiler\ast\VariableNode;
use xp\compiler\ast\ReturnNode;
use xp\compiler\Syntax;
use io\streams\MemoryInputStream;
use lang\ClassLoader;

class VisitorTest extends \unittest\TestCase {
  private static $visitor;

  #[@beforeClass]
  public static function defineVisitor() {
    self::$visitor= ClassLoader::defineClass('VisitorTest··Visitor', 'xp.compiler.ast.Visitor', [], '{
      public $visited= array();
      public function visitOne($node) {
        $this->visited[]= $node;
        return parent::visitOne($node);
      }
    }');
  }

  /**
   * Parse sourcecode
   *
   * @param  string $source
   * @return xp.compiler.ast.TypeDeclarationNode
   */
  private function parse($source) {
    return Syntax::forName('xp')->parse(new MemoryInputStream($source))->declaration;
  }

  /**
   * Assertion helper
   *
   * @param  xp.compiler.ast.Node[] $nodes
   * @param  xp.compiler.ast.Node $toVisit
   * @throws unittest.AssertionFailedError
   */
  private function assertVisited(array $nodes, Node $toVisit) {
    $visitor= self::$visitor->newInstance();
    $visitor->visitOne($toVisit);
    $this->assertEquals($nodes, $visitor->visited);
  }

  #[@test]
  public function visitAnnotation() {
    $node= new \xp\compiler\ast\AnnotationNode(['type' => 'deprecated', 'parameters' => []]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitAnnotationWithParameters() {
    $node= new \xp\compiler\ast\AnnotationNode(['type' => 'deprecated', 'parameters' => [
      new \xp\compiler\ast\StringNode('Use other class instead')
    ]]);
    $this->assertVisited([$node, $node->parameters[0]], $node);
  }

  #[@test]
  public function visitArm() {
    $node= new \xp\compiler\ast\ArmNode(
      [],
      [new VariableNode('in'), new VariableNode('out')],
      [new ReturnNode()]
    );
    $this->assertVisited(
      [$node, $node->variables[0], $node->variables[1], $node->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitArrayAccess() {
    $node= new \xp\compiler\ast\ArrayAccessNode(new VariableNode('a'), new IntegerNode(0));
    $this->assertVisited([$node, $node->target, $node->offset], $node);
  }

  #[@test]
  public function visitArrayAccessWithoutOffset() {
    $node= new \xp\compiler\ast\ArrayAccessNode(new VariableNode('a'), null);
    $this->assertVisited([$node, $node->target], $node);
  }

  #[@test]
  public function visitArray() {
    $node= new \xp\compiler\ast\ArrayNode(['values' => [new IntegerNode(0), new IntegerNode(1)]]);
    $this->assertVisited([$node, $node->values[0], $node->values[1]], $node);
  }

  #[@test]
  public function visitEmptyArray() {
    $node= new \xp\compiler\ast\ArrayNode(['values' => []]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitAssignment() {
    $node= new \xp\compiler\ast\AssignmentNode([
      'variable'   => new VariableNode('a'), 
      'expression' => new IntegerNode(0), 
      'op'         => '='
    ]);
    $this->assertVisited([$node, $node->variable, $node->expression], $node);
  }

  #[@test]
  public function visitBinaryOp() {
    $node= new \xp\compiler\ast\BinaryOpNode([
      'lhs' => new VariableNode('a'), 
      'rhs' => new IntegerNode(0), 
      'op'  => '+'
    ]);
    $this->assertVisited([$node, $node->lhs, $node->rhs], $node);
  }

  #[@test]
  public function visitBoolean() {
    $node= new \xp\compiler\ast\BooleanNode(true);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitBooleanOp() {
    $node= new \xp\compiler\ast\BooleanOpNode([
      'lhs' => new VariableNode('a'), 
      'rhs' => new IntegerNode(0), 
      'op'  => '&&'
    ]);
    $this->assertVisited([$node, $node->lhs, $node->rhs], $node);
  }

  #[@test]
  public function visitBreak() {
    $node= new \xp\compiler\ast\BreakNode();
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitCase() {
    $node= new \xp\compiler\ast\CaseNode([
      'expression' => new IntegerNode(0), 
      'statements' => [new VariableNode('a'), new \xp\compiler\ast\BreakNode()]
    ]);
    $this->assertVisited([$node, $node->expression, $node->statements[0], $node->statements[1]], $node);
  }

  #[@test]
  public function visitCast() {
    $node= new \xp\compiler\ast\CastNode([
      'expression' => new VariableNode('a'), 
      'type'       => new TypeName('int'),
      'check'      => true
    ]);
    $this->assertVisited([$node, $node->expression], $node);
  }

  #[@test]
  public function visitCatch() {
    $node= new \xp\compiler\ast\CatchNode([
      'type'       => new TypeName('int'),
      'variable'   => 'a',
      'statements' => [new VariableNode('a'), new \xp\compiler\ast\BreakNode()]
    ]);
    $this->assertVisited([$node, new VariableNode('a'), $node->statements[0], $node->statements[1]], $node);
  }

  #[@test]
  public function visitCatchWithEmptyStatements() {
    $node= new \xp\compiler\ast\CatchNode([
      'type'       => new TypeName('int'),
      'variable'   => 'a',
      'statements' => []
    ]);
    $this->assertVisited([$node, new VariableNode('a')], $node);
  }

  #[@test]
  public function visitMemberAccess() {
    $node= new \xp\compiler\ast\MemberAccessNode(new VariableNode('this'), 'member'); 
    $this->assertVisited([$node, $node->target], $node);
  }

  #[@test]
  public function visitMethodCall() {
    $node= new \xp\compiler\ast\MethodCallNode(new VariableNode('this'), 'method', [new VariableNode('a')]); 
    $this->assertVisited([$node, $node->target, $node->arguments[0]], $node);
  }

  #[@test]
  public function visitMethodCallWithEmptyArgumentList() {
    $node= new \xp\compiler\ast\MethodCallNode(new VariableNode('this'), 'method', []); 
    $this->assertVisited([$node, $node->target], $node);
  }

  #[@test]
  public function visitMethodCallWithNullArgumentList() {
    $node= new \xp\compiler\ast\MethodCallNode(new VariableNode('this'), 'method', null); 
    $this->assertVisited([$node, $node->target], $node);
  }

  #[@test]
  public function visitInstanceCall() {
    $node= new \xp\compiler\ast\InstanceCallNode(new VariableNode('this'), [new VariableNode('a')]); 
    $this->assertVisited([$node, $node->target, $node->arguments[0]], $node);
  }

  #[@test]
  public function visitInstanceCallWithEmptyArgumentList() {
    $node= new \xp\compiler\ast\InstanceCallNode(new VariableNode('this'), []); 
    $this->assertVisited([$node, $node->target], $node);
  }

  #[@test]
  public function visitInstanceCallWithNullArgumentList() {
    $node= new \xp\compiler\ast\InstanceCallNode(new VariableNode('this'), null); 
    $this->assertVisited([$node, $node->target], $node);
  }

  #[@test]
  public function visitStaticMemberAccess() {
    $node= new \xp\compiler\ast\StaticMemberAccessNode(new TypeName('self'), 'member'); 
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitStaticMethodCall() {
    $node= new \xp\compiler\ast\StaticMethodCallNode(new TypeName('self'), 'method', [new VariableNode('a')]); 
    $this->assertVisited([$node, $node->arguments[0]], $node);
  }

  #[@test]
  public function visitStaticMethodCallWithEmptyArgumentList() {
    $node= new \xp\compiler\ast\StaticMethodCallNode(new TypeName('self'), 'method', []); 
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitStaticMethodCallWithNullArgumentList() {
    $node= new \xp\compiler\ast\StaticMethodCallNode(new TypeName('self'), 'method', null); 
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitConstantAccess() {
    $node= new \xp\compiler\ast\ConstantAccessNode(new TypeName('self'), 'CONSTANT');
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitClassAccess() {
    $node= new \xp\compiler\ast\ClassAccessNode(new TypeName('self'));
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitClass() {
    $node= new \xp\compiler\ast\ClassNode(MODIFIER_PUBLIC, [], new TypeName('self'), null, [], [
      new \xp\compiler\ast\FieldNode(['name' => 'type', 'modifiers' => MODIFIER_PUBLIC]),
      new \xp\compiler\ast\FieldNode(['name' => 'name', 'modifiers' => MODIFIER_PUBLIC]),
    ]);
    $this->assertVisited([$node, $node->body[0], $node->body[1]], $node);
  }

  #[@test]
  public function visitClassWithEmptyBody() {
    $node= new \xp\compiler\ast\ClassNode(MODIFIER_PUBLIC, [], new TypeName('self'), null, []);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitClone() {
    $node= new \xp\compiler\ast\CloneNode(new VariableNode('this')); 
    $this->assertVisited([$node, $node->expression], $node);
  }

  #[@test]
  public function visitComparison() {
    $node= new \xp\compiler\ast\ComparisonNode([
      'lhs' => new VariableNode('a'), 
      'rhs' => new IntegerNode(0), 
      'op'  => '=='
    ]);
    $this->assertVisited([$node, $node->lhs, $node->rhs], $node);
  }

  #[@test]
  public function visitConstant() {
    $node= new \xp\compiler\ast\ConstantNode('STDERR');
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitConstructor() {
    $node= new \xp\compiler\ast\ConstructorNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'parameters' => null,
      'throws'     => null,
      'body'       => [new VariableNode('a'), new ReturnNode()],
      'extension'  => null
    ]);
    $this->assertVisited([$node, $node->body[0], $node->body[1]], $node);
  }

  #[@test]
  public function visitContinue() {
    $node= new \xp\compiler\ast\ContinueNode();
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitDefault() {
    $node= new \xp\compiler\ast\DefaultNode(['statements' => [new ReturnNode()]]);
    $this->assertVisited([$node, $node->statements[0]], $node);
  }

  #[@test]
  public function visitDecimal() {
    $node= new \xp\compiler\ast\DecimalNode(1.0);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitDo() {
    $node= new \xp\compiler\ast\DoNode(new VariableNode('continue'), [new VariableNode('a'), new ReturnNode()]);
    $this->assertVisited([$node, $node->statements[0], $node->statements[1], $node->expression], $node);
  }

  #[@test]
  public function visitElse() {
    $node= new \xp\compiler\ast\ElseNode(['statements' => [new VariableNode('a'), new ReturnNode()]]);
    $this->assertVisited([$node, $node->statements[0], $node->statements[1]], $node);
  }

  #[@test]
  public function visitEnumMember() {
    $node= new \xp\compiler\ast\EnumMemberNode(['name' => 'penny', 'body' => [new VariableNode('a'), new ReturnNode()]]);
    $this->assertVisited([$node, $node->body[0], $node->body[1]], $node);
  }

  #[@test]
  public function visitEnumMemberWithEmptyBody() {
    $node= new \xp\compiler\ast\EnumMemberNode(['name' => 'penny']);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitEnum() {
    $node= new \xp\compiler\ast\EnumNode(MODIFIER_PUBLIC, [], new TypeName('Coin'), null, [], [
      new \xp\compiler\ast\EnumMemberNode(['name' => 'penny']),
      new \xp\compiler\ast\EnumMemberNode(['name' => 'dime']),
    ]);
    $this->assertVisited([$node, $node->body[0], $node->body[1]], $node);
  }

  #[@test]
  public function visitField() {
    $node= new \xp\compiler\ast\FieldNode(['name' => 'type', 'modifiers' => MODIFIER_PUBLIC]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitFieldWithInitialization() {
    $node= new \xp\compiler\ast\FieldNode([
      'name'           => 'type', 
      'modifiers'      => MODIFIER_PUBLIC,
      'initialization' => new IntegerNode(0)
    ]);
    $this->assertVisited([$node, $node->initialization], $node);
  }

  #[@test]
  public function visitFinally() {
    $node= new \xp\compiler\ast\FinallyNode(['statements' => [new ReturnNode()]]);
    $this->assertVisited([$node, $node->statements[0]], $node);
  }

  #[@test]
  public function visitFinallyWithEmptyStatements() {
    $node= new \xp\compiler\ast\FinallyNode(['statements' => []]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitFor() {
    $node= new \xp\compiler\ast\ForNode([
      'initialization' => [new VariableNode('a')],
      'condition'      => [new VariableNode('b')],
      'loop'           => [new VariableNode('c')],
      'statements'     => [new VariableNode('d')], 
    ]);
    $this->assertVisited(
      [$node, $node->initialization[0], $node->condition[0], $node->loop[0], $node->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitForWithEmptyStatements() {
    $node= new \xp\compiler\ast\ForNode([
      'initialization' => [new VariableNode('a')],
      'condition'      => [new VariableNode('b')],
      'loop'           => [new VariableNode('c')],
      'statements'     => null, 
    ]);
    $this->assertVisited(
      [$node, $node->initialization[0], $node->condition[0], $node->loop[0]], 
      $node
    );
  }

  #[@test]
  public function visitForeachWithKey() {
    $node= new \xp\compiler\ast\ForeachNode([
      'expression'    => new VariableNode('list'),
      'assignment'    => ['value' => 'value'],
      'statements'    => [new VariableNode('c')], 
    ]);
    $this->assertVisited(
      [$node, $node->expression, new VariableNode('value'), $node->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitForeachWithKeyAndValue() {
    $node= new \xp\compiler\ast\ForeachNode([
      'expression'    => new VariableNode('map'),
      'assignment'    => ['key' => 'key', 'value' => 'value'],
      'statements'    => [new VariableNode('c')], 
    ]);
    $this->assertVisited(
      [$node, $node->expression, new VariableNode('key'), new VariableNode('value'), $node->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitForeachWithEmptyStatements() {
    $node= new \xp\compiler\ast\ForeachNode([
      'expression'    => new VariableNode('list'),
      'assignment'    => ['value' => 'value'],
      'statements'    => null, 
    ]);
    $this->assertVisited(
      [$node, $node->expression, new VariableNode('value')], 
      $node
    );
  }

  #[@test]
  public function visitHex() {
    $node= new \xp\compiler\ast\HexNode('0xFFFF');
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitIf() {
    $node= new \xp\compiler\ast\IfNode([
      'condition'      => new VariableNode('i'),
      'statements'     => [new ReturnNode()],
      'otherwise'      => null, 
    ]);
    $this->assertVisited(
      [$node, $node->condition, $node->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitIfWithElse() {
    $node= new \xp\compiler\ast\IfNode([
      'condition'      => new VariableNode('i'),
      'statements'     => [new ReturnNode()],
      'otherwise'      => new \xp\compiler\ast\ElseNode(['statements' => [new ReturnNode()]]), 
    ]);
    $this->assertVisited(
      [$node, $node->condition, $node->statements[0], $node->otherwise, $node->otherwise->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitIfWithEmptyStatements() {
    $node= new \xp\compiler\ast\IfNode([
      'condition'      => new VariableNode('i'),
      'statements'     => null,
      'otherwise'      => null, 
    ]);
    $this->assertVisited(
      [$node, $node->condition], 
      $node
    );
  }

  #[@test]
  public function visitImport() {
    $node= new \xp\compiler\ast\ImportNode(['name' => 'net.xp_lang.tests.StringBuffer']);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitIndexer() {
    $node= new \xp\compiler\ast\IndexerNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('T'),
      'parameter'  => [
        'name'  => 'offset',
        'type'  => new TypeName('int'),
        'check' => true
      ],
      'handlers'   => [
        'get'   => [new VariableNode('this')],
        'set'   => [new ReturnNode()],
      ]
    ]);
    $this->assertVisited([$node, $node->handlers['get'][0], $node->handlers['set'][0]], $node);
  }

  #[@test]
  public function visitIndexerWithEmptyHandlerBodies() {
    $node= new \xp\compiler\ast\IndexerNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('T'),
      'parameter'  => [
        'name'  => 'offset',
        'type'  => new TypeName('int'),
        'check' => true
      ],
      'handlers'   => [
        'get'   => null,
        'set'   => null
      ]
    ]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitInstanceCreation() {
    $node= new \xp\compiler\ast\InstanceCreationNode([
      'type'       => new TypeName('self'), 
      'parameters' => [new VariableNode('a')]
    ]);
    $this->assertVisited([$node, $node->parameters[0]], $node);
  }

  #[@test]
  public function visitInteger() {
    $node= new IntegerNode(1);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitInterface() {
    $node= new \xp\compiler\ast\InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Strings'), [], [
      new \xp\compiler\ast\ClassConstantNode('LF', new TypeName('string'), new StringNode('\n')),
      new \xp\compiler\ast\ClassConstantNode('CR', new TypeName('string'), new StringNode('\r'))
    ]);
    $this->assertVisited(
      [$node, $node->body[0], $node->body[0]->value, $node->body[1], $node->body[1]->value], 
      $node
    );
  }

  #[@test]
  public function visitInterfaceWithEmptyBody() {
    $node= new \xp\compiler\ast\InterfaceNode(MODIFIER_PUBLIC, [], new TypeName('Strings'), []);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitInstanceCreationWithBody() {
    $node= new \xp\compiler\ast\InstanceCreationNode([
      'type'       => new TypeName('self'), 
      'parameters' => [new VariableNode('a')],
      'body'       => [new ReturnNode()],
    ]);
    $this->assertVisited([$node, $node->parameters[0], $node->body[0]], $node);
  }

  #[@test]
  public function visitInvocation() {
    $node= new \xp\compiler\ast\InvocationNode('create', [new StringNode('new HashTable<string, string>')]);
    $this->assertVisited([$node, $node->arguments[0]], $node);
  }

  #[@test]
  public function visitLambda() {
    $node= new \xp\compiler\ast\LambdaNode([['name' => 'a']], [new ReturnNode()]);
    $this->assertVisited(
      [$node, $node->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitLambdaWithEmptyStatements() {
    $node= new \xp\compiler\ast\LambdaNode([['name' => 'a']], []);
    $this->assertVisited(
      [$node], 
      $node
    );
  }

  #[@test]
  public function visitEmptyMap() {
    $node= new \xp\compiler\ast\MapNode(['elements' => null]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitMap() {
    $node= new \xp\compiler\ast\MapNode(['elements' => [
      [
        new \xp\compiler\ast\StringNode('one'),
        new IntegerNode('1'),
      ],
    ]]);
    $this->assertVisited(
      [$node, $node->elements[0][0], $node->elements[0][1]], 
      $node
    );
  }

  #[@test]
  public function visitMethod() {
    $node= new \xp\compiler\ast\MethodNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('bool'),
      'parameters' => [[
        'name'  => 'cmp',
        'type'  => new TypeName('Generic'),
        'check' => false
      ]],
      'throws'     => null,
      'body'       => [new ReturnNode()],
      'extension'  => null
    ]);
    $this->assertVisited([$node, $node->body[0]], $node);
  }

  #[@test]
  public function visitMethodWithEmptyBody() {
    $node= new \xp\compiler\ast\MethodNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('bool'),
      'parameters' => [[
        'name'  => 'cmp',
        'type'  => new TypeName('Generic'),
        'check' => false
      ]],
      'throws'     => null,
      'body'       => [],
      'extension'  => null
    ]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitNativeImport() {
    $node= new \xp\compiler\ast\NativeImportNode(['name' => 'pcre.*']);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitNoop() {
    $node= new \xp\compiler\ast\NoopNode();
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitNull() {
    $node= new \xp\compiler\ast\NullNode();
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitOperator() {
    $node= new \xp\compiler\ast\OperatorNode([
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'name'       => '',
      'symbol'     => '~',
      'returns'    => new TypeName('self'),
      'parameters' => [
        ['name' => 'self', 'type' => new TypeName('self'), 'check' => true],
        ['name' => 'arg', 'type' => TypeName::$VAR, 'check' => true],
      ],
      'throws'     => null,
      'body'       => [new ReturnNode()],
    ]);
    $this->assertVisited([$node, $node->body[0]], $node);
  }

  #[@test]
  public function visitOperatorWithEmptyBody() {
    $node= new \xp\compiler\ast\OperatorNode([
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'name'       => '',
      'symbol'     => '~',
      'returns'    => new TypeName('self'),
      'parameters' => [
        ['name' => 'self', 'type' => new TypeName('self'), 'check' => true],
        ['name' => 'arg', 'type' => TypeName::$VAR, 'check' => true],
      ],
      'throws'     => null,
      'body'       => []
    ]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitPackage() {
    $node= new \xp\compiler\ast\PackageNode(['name' => 'com.example.*']);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitProperty() {
    $node= new \xp\compiler\ast\PropertyNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('string'),
      'name'       => 'name',
      'handlers'   => [
        'get' => [new ReturnNode()]
      ]
    ]);
    $this->assertVisited([$node, $node->handlers['get'][0]], $node);
  }

  #[@test]
  public function visitPropertyWithEmptyHandlerBody() {
    $node= new \xp\compiler\ast\PropertyNode([
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('string'),
      'name'       => 'name',
      'handlers'   => [
        'get' => null
      ]
    ]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitReturn() {
    $node= new ReturnNode();
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitReturnWithExpression() {
    $node= new ReturnNode(new VariableNode('a'));
    $this->assertVisited([$node, $node->expression], $node);
  }

  #[@test]
  public function visitStatements() {
    $node= new \xp\compiler\ast\StatementsNode([new VariableNode('a'), new ReturnNode()]);
    $this->assertVisited([$node, $node->list[0], $node->list[1]], $node);
  }

  #[@test]
  public function visitStatementsWithEmptyList() {
    $node= new \xp\compiler\ast\StatementsNode();
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitStaticImport() {
    $node= new \xp\compiler\ast\StaticImportNode(['name' => 'util.cmd.Console.*']);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitStaticInitializer() {
    $node= new \xp\compiler\ast\StaticInitializerNode([new VariableNode('a'), new ReturnNode()]);
    $this->assertVisited([$node, $node->statements[0], $node->statements[1]], $node);
  }

  #[@test]
  public function visitStaticInitializerWithEmptyStatements() {
    $node= new \xp\compiler\ast\StaticInitializerNode([]);
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitTernary() {
    $node= new \xp\compiler\ast\TernaryNode([
      'condition'   => new VariableNode('a'), 
      'expression'  => new VariableNode('a'), 
      'conditional' => new VariableNode('b')
    ]);
    $this->assertVisited(
      [$node, $node->condition, $node->expression, $node->conditional], 
      $node
    );
  }

  #[@test]
  public function visitString() {
    $node= new \xp\compiler\ast\StringNode('Hello World');
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitSwitch() {
    $node= new \xp\compiler\ast\SwitchNode([
      'expression'     => new VariableNode('i'),
      'cases'          => [
        new \xp\compiler\ast\DefaultNode(['statements' => [new \xp\compiler\ast\BreakNode()]])
      ]
    ]);
    $this->assertVisited(
      [$node, $node->expression, $node->cases[0], $node->cases[0]->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitTernaryWithoutExpression() {
    $node= new \xp\compiler\ast\TernaryNode([
      'condition'   => new VariableNode('a'), 
      'conditional' => new VariableNode('b')
    ]);
    $this->assertVisited(
      [$node, $node->condition, $node->conditional], 
      $node
    );
  }

  #[@test]
  public function visitThrow() {
    $node= new \xp\compiler\ast\ThrowNode(['expression' => new VariableNode('i')]);
    $this->assertVisited([$node, $node->expression], $node);
  }

  #[@test]
  public function visitTry() {
    $node= new \xp\compiler\ast\TryNode([
      'statements' => [new ReturnNode()], 
      'handling'   => [new \xp\compiler\ast\FinallyNode(['statements' => [new ReturnNode()]])]
    ]);
    $this->assertVisited(
      [$node, $node->statements[0], $node->handling[0], $node->handling[0]->statements[0]], 
      $node
    );
  }

  #[@test]
  public function visitUnaryOp() {
    $node= new \xp\compiler\ast\UnaryOpNode([
      'expression'    => new VariableNode('i'),
      'op'            => '++',
      'postfix'       => true
    ]);
    $this->assertVisited([$node, $node->expression], $node);
  }

  #[@test]
  public function visitVariable() {
    $node= new VariableNode('i');
    $this->assertVisited([$node], $node);
  }

  #[@test]
  public function visitWhile() {
    $node= new \xp\compiler\ast\WhileNode(new VariableNode('continue'), [new VariableNode('a'), new ReturnNode()]);
    $this->assertVisited([$node, $node->expression, $node->statements[0], $node->statements[1]], $node);
  }

  #[@test]
  public function visitWith() {
    $node= new \xp\compiler\ast\WithNode([new VariableNode('o')], [new ReturnNode()]);
    $this->assertVisited([$node, $node->assignments[0], $node->statements[0]], $node);
  }

  #[@test]
  public function visitWithWithEmptyStatements() {
    $node= new \xp\compiler\ast\WithNode([new VariableNode('o')], []);
    $this->assertVisited([$node, $node->assignments[0]], $node);
  }

  #[@test]
  public function visitBracedExpression() {
    $node= new \xp\compiler\ast\BracedExpressionNode(new VariableNode('o'));
    $this->assertVisited([$node, $node->expression], $node);
  }

  #[@test]
  public function findVariables() {
    $visitor= newinstance(Visitor::class, [], '{
      public $variables= array();
      protected function visitVariable(VariableNode $var) {
        $this->variables[$var->name]= true;
        return $var;
      }
    }');
    $visitor->visitOne($this->parse('class Test {
      public int add(int $a, int $b) {
        return $a + $b;
      }

      public int subtract(int $a, int $b) {
        return $a - $b;
      }
    }'));
    $this->assertEquals(['a', 'b'], array_keys($visitor->variables));
  }

  #[@test]
  public function renameVariables() {
    $visitor= newinstance(Visitor::class, [], '{
      protected $replacements;

      public function __construct() {
        $this->replacements= array("number" => new VariableNode("n"));
      }
      
      protected function visitMethod(MethodNode $node) {
        foreach ($node->parameters as $i => $parameter) {
          if (isset($this->replacements[$parameter["name"]])) {
            $node->parameters[$i]["name"]= $this->replacements[$parameter["name"]]->name;
          }
        }
        return parent::visitMethod($node);
      }

      protected function visitVariable(VariableNode $node) {
        return isset($this->replacements[$node->name])
          ? $this->replacements[$node->name]
          : $node
        ;
      }
    }');

    $this->assertEquals(
      $this->parse('class Fibonacci {
        public int fib(int $n) {
          return $n < 2 ? 1 : self::fib($n - 2) + self::fib($n - 1);
        }
      }'),
      $visitor->visitOne($this->parse('class Fibonacci {
        public int fib(int $number) {
          return $number < 2 ? 1 : self::fib($number - 2) + self::fib($number - 1);
        }
      }'))
    );
  }
}
