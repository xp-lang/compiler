<?php namespace xp\compiler\ast;

/**
 * Represents an assignment
 *
 * Examples:
 * ```php
 * $a= 5;     // variable: a, op: =, expression: 5
 * $a+= 10;   // variable: a, op: +=, expression: 10
 * ```
 *
 * Operator may be one of:
 * * `=`   : Assignment
 * * `+=`  : Addition
 * * `-=`  : Subtraction
 * * `*=`  : Multiplication
 * * `/=`  : Division
 * * `%=`  : Modulo
 * * `~=`  : Concatenation
 * * `|=`  : Or
 * * `&=`  : And
 * * `^=`  : XOr
 * * `>>=` : Shift-Right
 * * `<<=` : Shift-Left
 *
 * @test    xp://net.xp_lang.tests.syntax.xp.AssignmentTest
 */
class AssignmentNode extends Node {
  public $variable = null;
  public $op = null;
  public $expression = null;
}
