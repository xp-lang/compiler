<?php namespace xp\compiler\ast;

/**
 * Represents an annotation
 *
 * Example:
 * ```php
 * [@Deprecated('Use Type instead')]
 * ```
 *
 * * Deprecated is the type
 * * Parameters is the a map ("default" : "Use Type instead")
 *
 * @test    xp://net.xp_lang.tests.syntax.xp.AnnotationTest
 */
class AnnotationNode extends Node {
  public $type       = null;
  public $parameters = array();
}
