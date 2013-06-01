<?php
$f= function() use(&$f) {
  if (class_exists('ClassLoader', false)) {

    // Ensure delegates are set up. Unfortunately, the static initializer will 
    // be invoked twice - although with no effect, it's unnecessary nevertheless:/
    \lang\ClassLoader::__static();
    \lang\ClassLoader::registerLoader(\xp\compiler\JitClassLoader::instanceFor(''), true);
  } else {
    xp::$cli[]= $f;
  }
};
xp::$cli[]= $f;
