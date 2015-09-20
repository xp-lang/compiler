<?php namespace net\xp_lang\tests;

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
    $node= new \xp\compiler\ast\AnnotationNode(array('type' => 'deprecated', 'parameters' => array()));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitAnnotationWithParameters() {
    $node= new \xp\compiler\ast\AnnotationNode(array('type' => 'deprecated', 'parameters' => array(
      new \xp\compiler\ast\StringNode('Use other class instead')
    )));
    $this->assertVisited(array($node, $node->parameters[0]), $node);
  }

  #[@test]
  public function visitArm() {
    $node= new \xp\compiler\ast\ArmNode(
      array(),
      array(new VariableNode('in'), new VariableNode('out')),
      array(new ReturnNode())
    );
    $this->assertVisited(
      array($node, $node->variables[0], $node->variables[1], $node->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitArrayAccess() {
    $node= new \xp\compiler\ast\ArrayAccessNode(new VariableNode('a'), new IntegerNode(0));
    $this->assertVisited(array($node, $node->target, $node->offset), $node);
  }

  #[@test]
  public function visitArrayAccessWithoutOffset() {
    $node= new \xp\compiler\ast\ArrayAccessNode(new VariableNode('a'), null);
    $this->assertVisited(array($node, $node->target), $node);
  }

  #[@test]
  public function visitArray() {
    $node= new \xp\compiler\ast\ArrayNode(array('values' => array(new IntegerNode(0), new IntegerNode(1))));
    $this->assertVisited(array($node, $node->values[0], $node->values[1]), $node);
  }

  #[@test]
  public function visitEmptyArray() {
    $node= new \xp\compiler\ast\ArrayNode(array('values' => array()));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitAssignment() {
    $node= new \xp\compiler\ast\AssignmentNode(array(
      'variable'   => new VariableNode('a'), 
      'expression' => new IntegerNode(0), 
      'op'         => '='
    ));
    $this->assertVisited(array($node, $node->variable, $node->expression), $node);
  }

  #[@test]
  public function visitBinaryOp() {
    $node= new \xp\compiler\ast\BinaryOpNode(array(
      'lhs' => new VariableNode('a'), 
      'rhs' => new IntegerNode(0), 
      'op'  => '+'
    ));
    $this->assertVisited(array($node, $node->lhs, $node->rhs), $node);
  }

  #[@test]
  public function visitBoolean() {
    $node= new \xp\compiler\ast\BooleanNode(true);
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitBooleanOp() {
    $node= new \xp\compiler\ast\BooleanOpNode(array(
      'lhs' => new VariableNode('a'), 
      'rhs' => new IntegerNode(0), 
      'op'  => '&&'
    ));
    $this->assertVisited(array($node, $node->lhs, $node->rhs), $node);
  }

  #[@test]
  public function visitBreak() {
    $node= new \xp\compiler\ast\BreakNode();
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitCase() {
    $node= new \xp\compiler\ast\CaseNode(array(
      'expression' => new IntegerNode(0), 
      'statements' => array(new VariableNode('a'), new \xp\compiler\ast\BreakNode())
    ));
    $this->assertVisited(array($node, $node->expression, $node->statements[0], $node->statements[1]), $node);
  }

  #[@test]
  public function visitCast() {
    $node= new \xp\compiler\ast\CastNode(array(
      'expression' => new VariableNode('a'), 
      'type'       => new TypeName('int'),
      'check'      => true
    ));
    $this->assertVisited(array($node, $node->expression), $node);
  }

  #[@test]
  public function visitCatch() {
    $node= new \xp\compiler\ast\CatchNode(array(
      'type'       => new TypeName('int'),
      'variable'   => 'a',
      'statements' => array(new VariableNode('a'), new \xp\compiler\ast\BreakNode())
    ));
    $this->assertVisited(array($node, new VariableNode('a'), $node->statements[0], $node->statements[1]), $node);
  }

  #[@test]
  public function visitCatchWithEmptyStatements() {
    $node= new \xp\compiler\ast\CatchNode(array(
      'type'       => new TypeName('int'),
      'variable'   => 'a',
      'statements' => array()
    ));
    $this->assertVisited(array($node, new VariableNode('a')), $node);
  }

  #[@test]
  public function visitMemberAccess() {
    $node= new \xp\compiler\ast\MemberAccessNode(new VariableNode('this'), 'member'); 
    $this->assertVisited(array($node, $node->target), $node);
  }

  #[@test]
  public function visitMethodCall() {
    $node= new \xp\compiler\ast\MethodCallNode(new VariableNode('this'), 'method', array(new VariableNode('a'))); 
    $this->assertVisited(array($node, $node->target, $node->arguments[0]), $node);
  }

  #[@test]
  public function visitMethodCallWithEmptyArgumentList() {
    $node= new \xp\compiler\ast\MethodCallNode(new VariableNode('this'), 'method', array()); 
    $this->assertVisited(array($node, $node->target), $node);
  }

  #[@test]
  public function visitMethodCallWithNullArgumentList() {
    $node= new \xp\compiler\ast\MethodCallNode(new VariableNode('this'), 'method', null); 
    $this->assertVisited(array($node, $node->target), $node);
  }

  #[@test]
  public function visitInstanceCall() {
    $node= new \xp\compiler\ast\InstanceCallNode(new VariableNode('this'), array(new VariableNode('a'))); 
    $this->assertVisited(array($node, $node->target, $node->arguments[0]), $node);
  }

  #[@test]
  public function visitInstanceCallWithEmptyArgumentList() {
    $node= new \xp\compiler\ast\InstanceCallNode(new VariableNode('this'), array()); 
    $this->assertVisited(array($node, $node->target), $node);
  }

  #[@test]
  public function visitInstanceCallWithNullArgumentList() {
    $node= new \xp\compiler\ast\InstanceCallNode(new VariableNode('this'), null); 
    $this->assertVisited(array($node, $node->target), $node);
  }

  #[@test]
  public function visitStaticMemberAccess() {
    $node= new \xp\compiler\ast\StaticMemberAccessNode(new TypeName('self'), 'member'); 
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitStaticMethodCall() {
    $node= new \xp\compiler\ast\StaticMethodCallNode(new TypeName('self'), 'method', array(new VariableNode('a'))); 
    $this->assertVisited(array($node, $node->arguments[0]), $node);
  }

  #[@test]
  public function visitStaticMethodCallWithEmptyArgumentList() {
    $node= new \xp\compiler\ast\StaticMethodCallNode(new TypeName('self'), 'method', array()); 
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitStaticMethodCallWithNullArgumentList() {
    $node= new \xp\compiler\ast\StaticMethodCallNode(new TypeName('self'), 'method', null); 
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitConstantAccess() {
    $node= new \xp\compiler\ast\ConstantAccessNode(new TypeName('self'), 'CONSTANT');
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitClassAccess() {
    $node= new \xp\compiler\ast\ClassAccessNode(new TypeName('self'));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitClass() {
    $node= new \xp\compiler\ast\ClassNode(MODIFIER_PUBLIC, array(), new TypeName('self'), null, array(), array(
      new \xp\compiler\ast\FieldNode(array('name' => 'type', 'modifiers' => MODIFIER_PUBLIC)),
      new \xp\compiler\ast\FieldNode(array('name' => 'name', 'modifiers' => MODIFIER_PUBLIC)),
    ));
    $this->assertVisited(array($node, $node->body[0], $node->body[1]), $node);
  }

  #[@test]
  public function visitClassWithEmptyBody() {
    $node= new \xp\compiler\ast\ClassNode(MODIFIER_PUBLIC, array(), new TypeName('self'), null, array());
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitClone() {
    $node= new \xp\compiler\ast\CloneNode(new VariableNode('this')); 
    $this->assertVisited(array($node, $node->expression), $node);
  }

  #[@test]
  public function visitComparison() {
    $node= new \xp\compiler\ast\ComparisonNode(array(
      'lhs' => new VariableNode('a'), 
      'rhs' => new IntegerNode(0), 
      'op'  => '=='
    ));
    $this->assertVisited(array($node, $node->lhs, $node->rhs), $node);
  }

  #[@test]
  public function visitConstant() {
    $node= new \xp\compiler\ast\ConstantNode('STDERR');
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitConstructor() {
    $node= new \xp\compiler\ast\ConstructorNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'parameters' => null,
      'throws'     => null,
      'body'       => array(new VariableNode('a'), new ReturnNode()),
      'extension'  => null
    ));
    $this->assertVisited(array($node, $node->body[0], $node->body[1]), $node);
  }

  #[@test]
  public function visitContinue() {
    $node= new \xp\compiler\ast\ContinueNode();
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitDefault() {
    $node= new \xp\compiler\ast\DefaultNode(array('statements' => array(new ReturnNode())));
    $this->assertVisited(array($node, $node->statements[0]), $node);
  }

  #[@test]
  public function visitDecimal() {
    $node= new \xp\compiler\ast\DecimalNode(1.0);
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitDo() {
    $node= new \xp\compiler\ast\DoNode(new VariableNode('continue'), array(new VariableNode('a'), new ReturnNode()));
    $this->assertVisited(array($node, $node->statements[0], $node->statements[1], $node->expression), $node);
  }

  #[@test]
  public function visitElse() {
    $node= new \xp\compiler\ast\ElseNode(array('statements' => array(new VariableNode('a'), new ReturnNode())));
    $this->assertVisited(array($node, $node->statements[0], $node->statements[1]), $node);
  }

  #[@test]
  public function visitEnumMember() {
    $node= new \xp\compiler\ast\EnumMemberNode(array('name' => 'penny', 'body' => array(new VariableNode('a'), new ReturnNode())));
    $this->assertVisited(array($node, $node->body[0], $node->body[1]), $node);
  }

  #[@test]
  public function visitEnumMemberWithEmptyBody() {
    $node= new \xp\compiler\ast\EnumMemberNode(array('name' => 'penny'));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitEnum() {
    $node= new \xp\compiler\ast\EnumNode(MODIFIER_PUBLIC, array(), new TypeName('Coin'), null, array(), array(
      new \xp\compiler\ast\EnumMemberNode(array('name' => 'penny')),
      new \xp\compiler\ast\EnumMemberNode(array('name' => 'dime')),
    ));
    $this->assertVisited(array($node, $node->body[0], $node->body[1]), $node);
  }

  #[@test]
  public function visitField() {
    $node= new \xp\compiler\ast\FieldNode(array('name' => 'type', 'modifiers' => MODIFIER_PUBLIC));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitFieldWithInitialization() {
    $node= new \xp\compiler\ast\FieldNode(array(
      'name'           => 'type', 
      'modifiers'      => MODIFIER_PUBLIC,
      'initialization' => new IntegerNode(0)
    ));
    $this->assertVisited(array($node, $node->initialization), $node);
  }

  #[@test]
  public function visitFinally() {
    $node= new \xp\compiler\ast\FinallyNode(array('statements' => array(new ReturnNode())));
    $this->assertVisited(array($node, $node->statements[0]), $node);
  }

  #[@test]
  public function visitFinallyWithEmptyStatements() {
    $node= new \xp\compiler\ast\FinallyNode(array('statements' => array()));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitFor() {
    $node= new \xp\compiler\ast\ForNode(array(
      'initialization' => array(new VariableNode('a')),
      'condition'      => array(new VariableNode('b')),
      'loop'           => array(new VariableNode('c')),
      'statements'     => array(new VariableNode('d')), 
    ));
    $this->assertVisited(
      array($node, $node->initialization[0], $node->condition[0], $node->loop[0], $node->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitForWithEmptyStatements() {
    $node= new \xp\compiler\ast\ForNode(array(
      'initialization' => array(new VariableNode('a')),
      'condition'      => array(new VariableNode('b')),
      'loop'           => array(new VariableNode('c')),
      'statements'     => null, 
    ));
    $this->assertVisited(
      array($node, $node->initialization[0], $node->condition[0], $node->loop[0]), 
      $node
    );
  }

  #[@test]
  public function visitForeachWithKey() {
    $node= new \xp\compiler\ast\ForeachNode(array(
      'expression'    => new VariableNode('list'),
      'assignment'    => array('value' => 'value'),
      'statements'    => array(new VariableNode('c')), 
    ));
    $this->assertVisited(
      array($node, $node->expression, new VariableNode('value'), $node->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitForeachWithKeyAndValue() {
    $node= new \xp\compiler\ast\ForeachNode(array(
      'expression'    => new VariableNode('map'),
      'assignment'    => array('key' => 'key', 'value' => 'value'),
      'statements'    => array(new VariableNode('c')), 
    ));
    $this->assertVisited(
      array($node, $node->expression, new VariableNode('key'), new VariableNode('value'), $node->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitForeachWithEmptyStatements() {
    $node= new \xp\compiler\ast\ForeachNode(array(
      'expression'    => new VariableNode('list'),
      'assignment'    => array('value' => 'value'),
      'statements'    => null, 
    ));
    $this->assertVisited(
      array($node, $node->expression, new VariableNode('value')), 
      $node
    );
  }

  #[@test]
  public function visitHex() {
    $node= new \xp\compiler\ast\HexNode('0xFFFF');
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitIf() {
    $node= new \xp\compiler\ast\IfNode(array(
      'condition'      => new VariableNode('i'),
      'statements'     => array(new ReturnNode()),
      'otherwise'      => null, 
    ));
    $this->assertVisited(
      array($node, $node->condition, $node->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitIfWithElse() {
    $node= new \xp\compiler\ast\IfNode(array(
      'condition'      => new VariableNode('i'),
      'statements'     => array(new ReturnNode()),
      'otherwise'      => new \xp\compiler\ast\ElseNode(array('statements' => array(new ReturnNode()))), 
    ));
    $this->assertVisited(
      array($node, $node->condition, $node->statements[0], $node->otherwise, $node->otherwise->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitIfWithEmptyStatements() {
    $node= new \xp\compiler\ast\IfNode(array(
      'condition'      => new VariableNode('i'),
      'statements'     => null,
      'otherwise'      => null, 
    ));
    $this->assertVisited(
      array($node, $node->condition), 
      $node
    );
  }

  #[@test]
  public function visitImport() {
    $node= new \xp\compiler\ast\ImportNode(array('name' => 'net.xp_lang.tests.StringBuffer'));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitIndexer() {
    $node= new \xp\compiler\ast\IndexerNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('T'),
      'parameter'  => array(
        'name'  => 'offset',
        'type'  => new TypeName('int'),
        'check' => true
      ),
      'handlers'   => array(
        'get'   => array(new VariableNode('this')),
        'set'   => array(new ReturnNode()),
      )
    ));
    $this->assertVisited(array($node, $node->handlers['get'][0], $node->handlers['set'][0]), $node);
  }

  #[@test]
  public function visitIndexerWithEmptyHandlerBodies() {
    $node= new \xp\compiler\ast\IndexerNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('T'),
      'parameter'  => array(
        'name'  => 'offset',
        'type'  => new TypeName('int'),
        'check' => true
      ),
      'handlers'   => array(
        'get'   => null,
        'set'   => null
      )
    ));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitInstanceCreation() {
    $node= new \xp\compiler\ast\InstanceCreationNode(array(
      'type'       => new TypeName('self'), 
      'parameters' => array(new VariableNode('a'))
    ));
    $this->assertVisited(array($node, $node->parameters[0]), $node);
  }

  #[@test]
  public function visitInteger() {
    $node= new IntegerNode(1);
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitInterface() {
    $node= new \xp\compiler\ast\InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Strings'), array(), array(
      new \xp\compiler\ast\ClassConstantNode('LF', new TypeName('string'), new StringNode('\n')),
      new \xp\compiler\ast\ClassConstantNode('CR', new TypeName('string'), new StringNode('\r'))
    ));
    $this->assertVisited(
      array($node, $node->body[0], $node->body[0]->value, $node->body[1], $node->body[1]->value), 
      $node
    );
  }

  #[@test]
  public function visitInterfaceWithEmptyBody() {
    $node= new \xp\compiler\ast\InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Strings'), array());
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitInstanceCreationWithBody() {
    $node= new \xp\compiler\ast\InstanceCreationNode(array(
      'type'       => new TypeName('self'), 
      'parameters' => array(new VariableNode('a')),
      'body'       => array(new ReturnNode()),
    ));
    $this->assertVisited(array($node, $node->parameters[0], $node->body[0]), $node);
  }

  #[@test]
  public function visitInvocation() {
    $node= new \xp\compiler\ast\InvocationNode('create', array(new StringNode('new HashTable<string, string>')));
    $this->assertVisited(array($node, $node->arguments[0]), $node);
  }

  #[@test]
  public function visitLambda() {
    $node= new \xp\compiler\ast\LambdaNode(array(['name' => 'a']), array(new ReturnNode()));
    $this->assertVisited(
      array($node, $node->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitLambdaWithEmptyStatements() {
    $node= new \xp\compiler\ast\LambdaNode(array(['name' => 'a']), array());
    $this->assertVisited(
      array($node), 
      $node
    );
  }

  #[@test]
  public function visitEmptyMap() {
    $node= new \xp\compiler\ast\MapNode(array('elements' => null));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitMap() {
    $node= new \xp\compiler\ast\MapNode(array('elements' => array(
      array(
        new \xp\compiler\ast\StringNode('one'),
        new IntegerNode('1'),
      ),
    )));
    $this->assertVisited(
      array($node, $node->elements[0][0], $node->elements[0][1]), 
      $node
    );
  }

  #[@test]
  public function visitMethod() {
    $node= new \xp\compiler\ast\MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('bool'),
      'parameters' => array(array(
        'name'  => 'cmp',
        'type'  => new TypeName('Generic'),
        'check' => false
      )),
      'throws'     => null,
      'body'       => array(new ReturnNode()),
      'extension'  => null
    ));
    $this->assertVisited(array($node, $node->body[0]), $node);
  }

  #[@test]
  public function visitMethodWithEmptyBody() {
    $node= new \xp\compiler\ast\MethodNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'name'       => 'equals',
      'returns'    => new TypeName('bool'),
      'parameters' => array(array(
        'name'  => 'cmp',
        'type'  => new TypeName('Generic'),
        'check' => false
      )),
      'throws'     => null,
      'body'       => array(),
      'extension'  => null
    ));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitNativeImport() {
    $node= new \xp\compiler\ast\NativeImportNode(array('name' => 'pcre.*'));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitNoop() {
    $node= new \xp\compiler\ast\NoopNode();
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitNull() {
    $node= new \xp\compiler\ast\NullNode();
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitOperator() {
    $node= new \xp\compiler\ast\OperatorNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'name'       => '',
      'symbol'     => '~',
      'returns'    => new TypeName('self'),
      'parameters' => array(
        array('name' => 'self', 'type' => new TypeName('self'), 'check' => true),
        array('name' => 'arg', 'type' => TypeName::$VAR, 'check' => true),
      ),
      'throws'     => null,
      'body'       => array(new ReturnNode()),
    ));
    $this->assertVisited(array($node, $node->body[0]), $node);
  }

  #[@test]
  public function visitOperatorWithEmptyBody() {
    $node= new \xp\compiler\ast\OperatorNode(array(
      'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
      'annotations'=> null,
      'name'       => '',
      'symbol'     => '~',
      'returns'    => new TypeName('self'),
      'parameters' => array(
        array('name' => 'self', 'type' => new TypeName('self'), 'check' => true),
        array('name' => 'arg', 'type' => TypeName::$VAR, 'check' => true),
      ),
      'throws'     => null,
      'body'       => array()
    ));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitPackage() {
    $node= new \xp\compiler\ast\PackageNode(array('name' => 'com.example.*'));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitProperty() {
    $node= new \xp\compiler\ast\PropertyNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('string'),
      'name'       => 'name',
      'handlers'   => array(
        'get' => array(new ReturnNode())
      )
    ));
    $this->assertVisited(array($node, $node->handlers['get'][0]), $node);
  }

  #[@test]
  public function visitPropertyWithEmptyHandlerBody() {
    $node= new \xp\compiler\ast\PropertyNode(array(
      'modifiers'  => MODIFIER_PUBLIC,
      'annotations'=> null,
      'type'       => new TypeName('string'),
      'name'       => 'name',
      'handlers'   => array(
        'get' => null
      )
    ));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitReturn() {
    $node= new ReturnNode();
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitReturnWithExpression() {
    $node= new ReturnNode(new VariableNode('a'));
    $this->assertVisited(array($node, $node->expression), $node);
  }

  #[@test]
  public function visitStatements() {
    $node= new \xp\compiler\ast\StatementsNode(array(new VariableNode('a'), new ReturnNode()));
    $this->assertVisited(array($node, $node->list[0], $node->list[1]), $node);
  }

  #[@test]
  public function visitStatementsWithEmptyList() {
    $node= new \xp\compiler\ast\StatementsNode();
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitStaticImport() {
    $node= new \xp\compiler\ast\StaticImportNode(array('name' => 'util.cmd.Console.*'));
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitStaticInitializer() {
    $node= new \xp\compiler\ast\StaticInitializerNode(array(new VariableNode('a'), new ReturnNode()));
    $this->assertVisited(array($node, $node->statements[0], $node->statements[1]), $node);
  }

  #[@test]
  public function visitStaticInitializerWithEmptyStatements() {
    $node= new \xp\compiler\ast\StaticInitializerNode(array());
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitTernary() {
    $node= new \xp\compiler\ast\TernaryNode(array(
      'condition'   => new VariableNode('a'), 
      'expression'  => new VariableNode('a'), 
      'conditional' => new VariableNode('b')
    ));
    $this->assertVisited(
      array($node, $node->condition, $node->expression, $node->conditional), 
      $node
    );
  }

  #[@test]
  public function visitString() {
    $node= new \xp\compiler\ast\StringNode('Hello World');
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitSwitch() {
    $node= new \xp\compiler\ast\SwitchNode(array(
      'expression'     => new VariableNode('i'),
      'cases'          => array(
        new \xp\compiler\ast\DefaultNode(array('statements' => array(new \xp\compiler\ast\BreakNode())))
      )
    ));
    $this->assertVisited(
      array($node, $node->expression, $node->cases[0], $node->cases[0]->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitTernaryWithoutExpression() {
    $node= new \xp\compiler\ast\TernaryNode(array(
      'condition'   => new VariableNode('a'), 
      'conditional' => new VariableNode('b')
    ));
    $this->assertVisited(
      array($node, $node->condition, $node->conditional), 
      $node
    );
  }

  #[@test]
  public function visitThrow() {
    $node= new \xp\compiler\ast\ThrowNode(array('expression' => new VariableNode('i')));
    $this->assertVisited(array($node, $node->expression), $node);
  }

  #[@test]
  public function visitTry() {
    $node= new \xp\compiler\ast\TryNode(array(
      'statements' => array(new ReturnNode()), 
      'handling'   => array(new \xp\compiler\ast\FinallyNode(array('statements' => array(new ReturnNode()))))
    ));
    $this->assertVisited(
      array($node, $node->statements[0], $node->handling[0], $node->handling[0]->statements[0]), 
      $node
    );
  }

  #[@test]
  public function visitUnaryOp() {
    $node= new \xp\compiler\ast\UnaryOpNode(array(
      'expression'    => new VariableNode('i'),
      'op'            => '++',
      'postfix'       => true
    ));
    $this->assertVisited(array($node, $node->expression), $node);
  }

  #[@test]
  public function visitVariable() {
    $node= new VariableNode('i');
    $this->assertVisited(array($node), $node);
  }

  #[@test]
  public function visitWhile() {
    $node= new \xp\compiler\ast\WhileNode(new VariableNode('continue'), array(new VariableNode('a'), new ReturnNode()));
    $this->assertVisited(array($node, $node->expression, $node->statements[0], $node->statements[1]), $node);
  }

  #[@test]
  public function visitWith() {
    $node= new \xp\compiler\ast\WithNode(array(new VariableNode('o')), array(new ReturnNode()));
    $this->assertVisited(array($node, $node->assignments[0], $node->statements[0]), $node);
  }

  #[@test]
  public function visitWithWithEmptyStatements() {
    $node= new \xp\compiler\ast\WithNode(array(new VariableNode('o')), array());
    $this->assertVisited(array($node, $node->assignments[0]), $node);
  }

  #[@test]
  public function visitBracedExpression() {
    $node= new \xp\compiler\ast\BracedExpressionNode(new VariableNode('o'));
    $this->assertVisited(array($node, $node->expression), $node);
  }

  #[@test]
  public function findVariables() {
    $visitor= newinstance('xp.compiler.ast.Visitor', array(), '{
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
    $this->assertEquals(array('a', 'b'), array_keys($visitor->variables));
  }

  #[@test]
  public function renameVariables() {
    $visitor= newinstance('xp.compiler.ast.Visitor', array(), '{
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
