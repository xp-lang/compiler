XP Language
===========
[![Build Status](https://secure.travis-ci.org/xp-lang/compiler.png)](https://travis-ci.org/xp-lang/compiler)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.3+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_3plus.png)](http://php.net/)

XP language is a feature-rich, typed and compiled programming language, based on the popular PHP language and designed to syntactically support features of the XP Framework. The source you write and compile with it can make use of the XP Framework's foundation classes. As the language itself is written in the XP Framework, no binary or proprietary extensions are needed! 

Installing
----------
Use the XP Installer to add this module as follows:

```sh
$ cd ~/.xp

# First installation
$ xpi add xp-lang/compiler

# Later on
$ xpi upgrade xp-lang/compiler
```

*Note: It is assumed you are using `~./xp` as path for your globally available XP modules, and have this path inside your xp.ini's `use` statement.*

Getting started
---------------
Like in the XP framework, the entry point is always a class. In their most simple form, these classes have a static main() method. To try it out, create a file called `HelloWorld.xp` with the following contents:

```groovy
public class HelloWorld {
  public static void main(string[] $args) {
    util.cmd.Console::writeLine('Hello World!');
  }
}
```

Then compile and run it!

```sh
$ xcc HelloWorld.xp
...

$ xp HelloWorld
Hello World!
```

### Differences
The things you will have noticed are:

* Classes may also have modifiers.
* The `extends Object` is optional and added by the compiler if omitted.
* The keyword `function` is gone and replaced by the return type. Because the main() method does not return anything, we use void.
* An array type is written as component[]
* Variables still have dollar signs. This makes it easy to spot them, that's why we've decided to keep this!
* Fully qualified classnames are written with dots.
* The object operator is also a dot (at the same time, the string concatenation operator is now the tilde, `~`).

### Features
The XP Language features - among others - support for the following:

* Namespaces (which are called packages)
* Imports, static imports and "on-demand" imports (`import util.*;`)
* Varargs syntax
* Distinguishable types for arrays and maps
* Class literal `::class`, and `finally` - also for PHP < 5.5!
* Properties with `get` and `set`, and Indexers
* Syntactic support for the following XP Framework features: Typesafe enumerations, Annotations, Generics, the `with` statement and a throws clause

### Further reading
To get an overview of XP Language's features, these are good reads:

* [The XP Language Wiki](https://github.com/xp-lang/compiler/wiki)
* [RFC #0052: Make XP its own (compiled) language](https://github.com/xp-framework/rfc/issues/52)
* [XP Blog: Language](http://news.planet-xp.net/category/17/Language/)

Developing
----------
In order to change XP Language and/or the Compiler **itself**, you need to clone this repository, e.g. using Git Read-Only:

```sh
$ cd [path]
$ git clone git://github.com/xp-framework/xp-language.git
```

### Directory structure
```
[path]/compiler
 |- ChangeLog.md      # Version log
 |- README.md         # This file
 |- module.pth        # Module classpath
 `- src               # Sourcecode, by Maven conventions
    |- main
    |  `- php
    `- test
       |- php
       `- config      # Unittest configuration
```

### Using it
Add that path to your `use` setting before the global module path inside your `xp.ini`, e.g.:

```ini
use=~/devel/xp-framework/core:~/devel/compiler:~/.xp
                              ^^^^^^^^^^^^^^^^
```

*On Windows systems, use `;` as separator*

**Enjoy!**

Contributing
------------
To contribute, use the GitHub way - fork, hack, and submit a pull request!

