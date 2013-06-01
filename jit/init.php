<?php
$f= function() use($init, &$f) {
  if (class_exists('ClassLoader', false)) {
    class JitClassLoader extends \lang\Object implements \lang\IClassLoader {
      public function providesClass($class) {
        fputs(STDERR, "P? $class\n");
        return false;
      }
      public function providesResource($filename) {
        return false;
      }
      public function providesPackage($package) {
        return false;   // TBI
      }
      public function packageContents($package) {
        return array(); // TBI
      }
      public function loadClass($class) {
        return new XPClass($this->loadClass0($class));
      }
      public function loadClass0($class) {
        throw new \lang\ClassNotFoundException($class);
      }
      public function getResource($string) {
        throw new \lang\ElementNotFoundException($string);
      }
      public function getResourceAsStream($string) {
        throw new \lang\ElementNotFoundException($string);
      }
      public function instanceId() {
        return "jit";
      }
    };
    \lang\ClassLoader::registerLoader(new JitClassLoader());
  } else {
    xp::$cli[]= $f;
  }
};
xp::$cli[]= $f;
