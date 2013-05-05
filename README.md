XP Language
===========
[![Build Status](https://secure.travis-ci.org/xp-framework/xp-language.png)](http://travis-ci.org/xp-framework/xp-language)

XP language is a feature-rich, typed and compiled programming language, based on the popular PHP language and designed to syntactically support features of the XP Framework. The source you write and compile with it can make use of the XP Framework's foundation classes. As the language itself is written in the XP Framework, no binary or proprietary extensions are needed! 

Installing
----------
Use the XP Installer to add this module as follows:

```sh
$ cd ~/.xp
$ xpi add xp-framework/xp-language
```

*Note: It is assumed you are using `~./xp` as path for your globally available XP modules, and have this path inside your xp.ini's `use` statement.*

Getting started
---------------
Create a file called `HelloWorld.xp` with the following contents:

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
$ xp HelloWorld
Hello World!
```

Developing
----------
Clone this repository, e.g. using Git Read-Only:

```sh
$ cd [path]
$ git clone git://github.com/xp-framework/xp-language.git
```

### Directory structure
```
[path]/xp-language
`- compiler
   |- ChangeLog         # Version log
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
use=~/devel/xp-framework/core:~/devel/xp-language/compiler:~/.xp
                              ^^^^^^^^^^^^^^^^^^^^^^^^^^^^
```

*On Windows systems, use `;` as separator*

**Enjoy!**

Contributing
------------
To contribute, use the GitHub way - fork, hack, and submit a pull request!
