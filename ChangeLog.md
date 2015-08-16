XP Compiler ChangeLog
========================================================================

## ?.?.? / ????-??-??

* Fixed issue #39: HHVM support
  . Fixed sscanf() incompatibilities - HHVM doesn't support `%*` for not
    assigning variables. Worked around by supplying dummy variables.
  . Added a native importer which doesn't verify extensions. HHVM does
    not group functions into extensions like PHP does.
  (@thekid)
* **Removed support for PHP 5.2 and PHP 5.3**:
  . Added new PHP 5.4 the default emitter, which emits `new T().method()`
    as `(new T())->method()` instead of wrapping it in the deprecated
    `create` core functionality.
  . Removed PHP 5.2 and 5.3 emitters, using `-E php5.2` or `-E php5.3`
    on the command line will produce an error!
  (@thekid)
* Improved performance when loading supported syntaxes by deferring
  parser and lexer instantiation until when first needed.
  (@thekid)

## 2.1.0 / 2015-08-16

* Adopted to newer XP versions by no longer using deprecated functionality:
  . Replaced `uses()` call with class loader API call
  . Replaced `raise()` with reflection-based creation statement
  . Changed `delete()` call to use `unset` language construct.
  (@thekid)

## 2.0.0 / 2015-03-01

* Made available via Composer (@thekid)
* **Heads up**: Changed minimum XP version to run the XP compiler to XP
  6.0.0. It can still generated code to run on XP 5.X-SERIES, but will
  require 6.X-SERIES to run itself (@thekid)
* Added possibility to add default values to lambda parameters. See 
  xp-lang/compiler#38 - (@thekid)
* Fixed compiler choking on methods with generic return types (@thekid)
* Allowed omitting the parameter type inside method declarations. The type
  will then be set to `var` and will be unchecked at runtime (@thekid)
* Changed syntax for lambdas from `#{ $a -> $a + 1 };` to `$a -> $a + 1;`,
  after resolving the grammar conflicts the former was avoiding. The old
  form is still supported but will raise compile-time warnings. It will be
  removed in the next major release.
  (@thekid)
* Changed PHP 5.3 emitter to emit PHP 5.3 anonymous functions for lambdas
  (@thekid)
* Merged pull request #37 to solve issue #36: Support "new T()", "T::const"
  and "T::$static" inside annotations - (@thekid)
* Added backwards compatible emitter (-E php5.2) to create non-namespaced
  code for use with XP 5.8 and PHP 5.2 - (@thekid)
* Changed default behaviour to creating namespaced code (compatible with 
  XP 5.9 and PHP 5.3+) - (@thekid)
  http://news.planet-xp.net/article/551/2013/10/10/
* Changed "-E" command line argument to support versions: `-E name` will 
  load xp.compiler.emit.name.Emitter, `-E name60` will load the class
  xp.compiler.emit.name.V60Emitter - (@thekid)
* Changed all emit*() methods in xp.compiler.emit.Emitter to protected
  (@thekid)

## 1.11.1 / 2013-10-06

* Changed runner to create unique class names in `xcc [-e|-w]` for testing
  reasons - (@thekid)
* Fixed anonymous instance creation to create unique class names, thus
  preventing "Fatal error: Cannot redeclare class ..." errors.
  (@thekid)

## 1.11.0 / 2013-10-05

* Heads up: Changed xp.compiler.types.Types implementations to return an 
  array of Parameter objects holding name, type and optional default value 
  instead of just the parameters' types - (@thekid)
* Made error message more verbose when syntax cannot be determined from
  file referenced by command line argument - (@thekid)
* Fixed Fatal error: Class 'xp\compiler\ClassLoader' not found when using
  command line argument `-cp` - (@thekid)

## 1.10.4 / 2013-06-02

* Fixed properties inconsistency - see pull request #35 (@thekid)
* Implemented RFC 250: Static member import syntax change (@thekid)

## 1.10.3 / 2013-05-27

* Implemented RFC 274: XP Language Repository reorganization (@thekid)

## 1.10.2 / 2013-05-20

* Implemented RFC 273: ChangeLog formatting - (@thekid)
* Fixed issue #32 - Fatal error during compilation (@thekid)

## 1.10.1 / 2013-05-14

* Changed emitter command line argument from "-e" to "-E"! See pull 
  request #30 - "-e" is now for command line code evaluation. - (@thekid)
* Implemented RFC 0249: Exponentiation operator - (@thekid)
* Implemented RFC 0238: xcc -e and xcc -w (command line code evaluation) - (@thekid)
* Fixed `this()` core functionality not being correctly resolved - (@thekid)
* Fixed fatal error in toString() of xp.compiler.types.* member classes - (@thekid)
* Fixed bug in PHP syntax with concatenation operator precedence - (@thekid)
* Fixed issue #31: Backslash in XP Language not considered a syntax error - (@thekid)
* Added command line option "-q" which will show only compilation errors - (@thekid)

## 1.10.0 / 2013-05-12

* Changed tests using `foreach` to use @values annotation. Requires 
  parameterized unittest feature (RFC 0267) and thus XP 5.9.1 - (@thekid)
* Converted the code base to namespaces - (@thekid)
* Implemented RFC 0252: Fluent interface compact syntax - (@thekid)
* Implemented RFC 0241: Compact method syntax - (@thekid)
* Implemented RFC 0240: Compact assignment syntax - (@thekid)
* Fixed issue #25: Resolving abstract enum members fails - (@thekid)
* Fixed various errors resulting from the conversion of the code base 
  to namespaces. Most typical: catch (Throwable $e) not catching any-
  thing, because the name "Throwable" is not present in this namespace. - (@thekid)
* Fixed issue #24: Fatal error with PHP 5.3.0 - (@thekid)
* Fixed PHP 5.3 namespaces fully qualified class names - (@thekid)
* Changed TypeReflection to determine enum member type correctly
  See pull request #23 - (@thekid)
* Implemented the navigation operator `?.` - see pull request #13 - (@thekid)

## 1.9.0 / 2013-05-05

* Gave the GitHub repository's homepage a makeover, adding installation,
  usage and contribution overviews and a small sourcecode example. See
  https://github.com/xp-framework/xp-language - (@thekid)
* Changed code no longer to use xp::$registry but instead the flattened
  version. This bumps the minimum XP Framework version to XP 5.9.0.
  See also https://github.com/xp-framework/xp-framework/pull/270 - (@thekid)
* Version now detached from framework - XP compiler v1.9.0 will be the 
  correct version for XP Framework v5.9.0 - (@thekid)
* Implemented RFC 0260: XP 5.9 as default branch - (@thekid)
* Work around uniqid() no longer generating unique ids.
  See https://github.com/xp-framework/xp-framework/issues/260 - (@thekid)
* Added Travis-CI configuration and setup commit hook accordingly
  http://news.planet-xp.net/article/520/2013/05/05/ - (@thekid)
* Added support for "self" keyword in parameters and return types - (@thekid)

## 1.2.0 / 2012-09-30

* Implemented RFC 0218: Parameter annotations
  http://news.planet-xp.net/article/480/2012/09/30/ - (@thekid)
* Refrain from resolving constants. Fixes issue #20 "the hard way" - the 
  optimization is removed all the way - (@thekid)
* Splitted compiler.ini and reorganize into src/test/resources
  # See xp-framework/xp-framework#160 - (@thekid)

## 1.1.2 / 2012-07-09

* Fixed indexers and operators inheritance - (@thekid)
* Added Maven pom - (@mrosoiu)

## 1.1.1 / 2012-06-08

* Added tests for hex addition oddities
  http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/#numbers
  See also PHP Bug #61095 and PHP Bug #61095 - (@thekid)
* Added support for hexadecimal escape sequences
  `\x   hex-digit   hex-digitopt   hex-digitopt   hex-digitopt` - (@thekid)
* Added support for exponent and octal number notation - (@thekid)
* RFC 0218: Added syntactical support for annotations with target - (@thekid)
* Fixed interface methods check. The following was disallowed but shouldn't
  public interface Test { void testIt(); } - (@thekid)
* Fixed extension methods - (@thekid)
* Refactored so that uses() will be emitted once at the top of the file
  and include all dependencies merged together. - (@thekid)
* Added logic to exclude bootstrap classes from being listed in uses(),
  they are globally available. Increases generated code's loading
  performance. - (@thekid)

## 1.0.2 / 2011-12-08

* Fixed arrays of array, map and generic types not being supported - (@thekid)
* Added field annotations - (@mrosoiu)

## 1.0.1 / 2011-09-07

* Fixed weird bug with compiled types being overwritten - (@thekid)
* Migrated repository to github.com (for the records) - (@thekid, @iigorr, @kiesel, @invadersmustdie)

## 1.0.0 / 2011-01-11

* Changed XP language to depend on 5.8.0 - (@thekid)
* Implemented RFC 0052 - Make XP its own (compiled) language - (@thekid)
* Fixed chaining after function calls - (@thekid)
* Fix assignments to assignments ($i= $j= 0; e.g.) being parsed in
  incorrect order - (@thekid)
* Changed map syntax to also support keys without quotes - (@thekid)
* Improved performance in self / parent lookup - (@thekid)
* Implemented auto-properties: public int id { get; set; }
  Equivalent of declaring a member variable and using an assignment
  inside set { } to $value and and a return statement with the member
  inside get { } - (@thekid)
* Implemented dynamic instance creation - (@thekid)
* Implemented dynamic member access - (@thekid)
* Added "-v" command line option for verbose output - (@thekid)
* Implemented ARM blocks in XP language, supported by lang.Closeable 
  interface in framework.
  http://news.planet-xp.net/article/397/2010/12/31/ - (@thekid)
* Implemented operator overloading - Works only inside XP language! - (@thekid)

## 0.9.2 / 2010-03-06

* Added |, &, ^, << and >> operators as well as their assignments - (@thekid)
* Implemented class constants
  Class constants are limited to numbers, booleans and strings but provide
  a cheap way of extracting magic constants from business logic. If you 
  require more flexibility, use static fields - (@thekid)
* Enabled creation of package classes (see RFC 0037) via "package class" - (@thekid)

## 0.9.1 / 2010-02-14

* Implemented unchecked casts (EXPR as TYPE?)
  Good for: return $a instanceof self && ($a as self?).compareTo(...). Now 
  it wouldn`t make sense to cast-check $a at runtime because it can NEVER 
  be anything else than of type "self" after the && (we checked it right 
  before) - but the compiler cannot know that:) - (@thekid)

## 0.9.0 / 2010-02-05

* Removed last OEL leftovers - emitter and tests - (@thekid)

## 0.8.0 / 2009-10-30

* Fixed type calculation in chains like $class.getMethods()[0].getName(); - (@thekid)
* Implemented with statement - e.g. with ($child= $n.addChild(new Node()) { ... } - (@thekid)

## 0.7.1 / 2009-10-17

* Changed runner to ext with non-zero exitcode if any of the compilation
  tasks fail (e.g. xcc src/A.xp src/B.xp - if A.xp fails, the entire
  run is marked as failed). Can be used for chaining xcc [files] && echo 
  "OK", also useful in makefiles which will stop after failure - (@thekid)

## 0.7.0 / 2009-10-11

* Implement lambdas - (@thekid)

## 0.6.0 / 2009-10-05

* Defaulted emitter to "source"
  To compile w/ oel, use `xcc -e oel [target [target [...]]] - (@thekid)

## 0.5.0 / 2009-05-03

* Implemented or-equal (|=), and-equal(&=), xor-equal (^=) and 
  div-equal (/=) - (@thekid)
* Created own nodes for true, false and null, refactored numbers into:
  . NumberNode
  |- NaturalNode
  |  - IntegerNode
  |  - HexNode
  |- DecimalNode - (@thekid)

## 0.4.0 / 2009-04-10

* Implemented try ... finally (without catch) - (@thekid)

## 0.3.0 / 2009-03-26

* First shot: Added PHP grammar for XP classes - 5.3+ w/o alternative syntax - (@thekid)

## 0.2.0 / 2009-03-08

* Changed syntax for maps from "{ key => value }" to "[ key : value ]"
  Inspired by http://groovy.codehaus.org/Collections#Collections-Maps - (@thekid)

## 0.1.0 / 2009-03-08

* Used "." as object operator instead of "->" - (@thekid)
* Changed foreach syntax from foreach ([expression] as [assignment]) to 
  foreach ([assignment] in [expression]) - (@thekid)
* Implemented varargs, annotations, anonymous instance creaton, properties,
  indexers, properties (via __get and __set), ?: shorthand - (@thekid)
* Implemented class literal - (@thekid)
* Implemented "import native" and "import static" - (@thekid)
* Made it possible to pass either:
  . XP source file (.xp)
  . PHP source file with XP class (.class.php)
  . Fully qualified class name - (@thekid)
* Added optimizations for:
  . Concatenating two constant values
  . Adding two constant values
  . Subtracting two constant values
  . Multiplying two constant values
  . Dividing two constant values - (@thekid)
* Implemented $array->length - (@thekid)

## 0.0.2 / 2008-02-20

* Added support for generic syntax - (@thekid)
* Added support for finally - (@thekid)
* Added initial implementation of map literals (require a prefix "map") - (@thekid)

## 0.0.1 / 2008-01-03

* Initial release: Prototype and proof of concept - (@thekid)