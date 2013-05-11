<?php namespace xp\compiler\optimize;

use xp\compiler\ast\StringNode;
use xp\compiler\ast\IntegerNode;
use xp\compiler\ast\DecimalNode;
use xp\compiler\ast\NumberNode;
use xp\compiler\ast\NaturalNode;
use xp\compiler\ast\BinaryOpNode;
use xp\compiler\ast\UnaryOpNode;
use xp\compiler\ast\BracedExpressionNode;
use xp\compiler\ast\Resolveable;

/**
 * Optimizes binary operations
 *
 * @see      http://en.wikipedia.org/wiki/Constant_folding
 * @test     xp://tests.optimization.BinaryOptimizationTest
 */
class BinaryOptimization extends \lang\Object implements Optimization {
  protected static $optimizable= array(
    '~'   => 'concat',
    '-'   => 'subtract',
    '+'   => 'add',
    '*'   => 'multiply',
    '/'   => 'divide',
    '%'   => 'modulo',
    '<<'  => 'shl',
    '>>'  => 'shr',
    '&'   => 'and',
    '|'   => 'or',
    '^'   => 'xor'
  );      
  protected static $switch= array(
    '-'   => '+',
    '+'   => '-'
  );
  
  /**
   * Evaluate concatenation
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalConcat(Resolveable $l, Resolveable $r) {
    return new StringNode($l->resolve().$r->resolve());
  }
  
  /**
   * Evaluate addition
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalAdd(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() + $r->resolve());
    } else if (($l instanceof DecimalNode && $r instanceof NumberNode) || ($l instanceof NumberNode && $r instanceof DecimalNode)) {
      return new DecimalNode($l->resolve() + $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate subtraction
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalSubtract(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() - $r->resolve());
    } else if (($l instanceof DecimalNode && $r instanceof NumberNode) || ($l instanceof NumberNode && $r instanceof DecimalNode)) {
      return new DecimalNode($l->resolve() - $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate multiplication
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalMultiply(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() * $r->resolve());
    } else if (($l instanceof DecimalNode && $r instanceof NumberNode) || ($l instanceof NumberNode && $r instanceof DecimalNode)) {
      return new DecimalNode($l->resolve() * $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate division
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalDivide(Resolveable $l, Resolveable $r) {
    if ($l instanceof NumberNode && $r instanceof NumberNode) {
      return new DecimalNode($l->resolve() / $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate modulo
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalModulo(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() % $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate shift right
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalShr(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() >> $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate shift left
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalShl(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() << $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate and ("&")
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalAnd(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() & $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate or ("|")
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalOr(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() | $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }

  /**
   * Evaluate xor ("^")
   *
   * @param   xp.compiler.ast.Resolveable l
   * @param   xp.compiler.ast.Resolveable r
   * @return  xp.compiler.ast.Node result
   */
  protected function evalXOr(Resolveable $l, Resolveable $r) {
    if ($l instanceof NaturalNode && $r instanceof NaturalNode) {
      return new IntegerNode($l->resolve() ^ $r->resolve());
    } else {
      return null;  // Not optimizable
    }
  }
  
  /**
   * Return node this optimization works on
   *
   * @return  lang.XPClass<? extends xp.compiler.ast.Node>
   */
  public function node() {
    return \lang\XPClass::forName('xp.compiler.ast.BinaryOpNode');
  }
  
  /**
   * Unwrap braced expressions
   *
   * @param   xp.compiler.ast.Node node
   * @return  xp.compiler.ast.Node node
   */
  protected function unwrap($node) {
    return $node instanceof BracedExpressionNode ? $node->expression : $node;
  }
  
  /**
   * Optimize a given node
   *
   * @param   xp.compiler.ast.Node in
   * @param   xp.compiler.types.Scope scope
   * @param   xp.compiler.optimize.Optimizations optimizations
   * @param   xp.compiler.ast.Node optimized
   */
  public function optimize(\xp\compiler\ast\Node $in, \xp\compiler\types\Scope $scope, Optimizations $optimizations) {
    if (!isset(self::$optimizable[$in->op])) return $in;
    
    $in->lhs= $optimizations->optimize($this->unwrap($in->lhs), $scope);
    $in->rhs= $optimizations->optimize($this->unwrap($in->rhs), $scope);

    // Optimize "a + -b" to "a - b" and "a - -b" to "a + b"
    if ($in->rhs instanceof UnaryOpNode && '-' === $in->rhs->op && isset(self::$switch[$in->op])) {
      $in= new BinaryOpNode(array(
        'lhs' => $in->lhs, 
        'rhs' => $in->rhs->expression,
        'op'  => self::$switch[$in->op]
      ));
    }

    // Constant folding
    if ($in->lhs instanceof Resolveable && $in->rhs instanceof Resolveable) {
      $r= call_user_func_array(array($this, 'eval'.self::$optimizable[$in->op]), array($in->lhs, $in->rhs));
      if (null !== $r) $in= $r;
    }

    return $in;
  }
}
