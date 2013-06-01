<?php namespace xp\compiler;

use lang\ClassLoader;

$f= function() use(&$f) {
  if (class_exists('ClassLoader', false)) {

    // Ensure delegates are set up. Unfortunately, the static initializer will 
    // be invoked twice - although with no effect, it's unnecessary nevertheless:/
    ClassLoader::__static();
    ClassLoader::registerLoader(JitClassLoader::instanceFor(realpath('.')), true);
  } else {
    \xp::$cli[]= $f;
  }
};
$f();
