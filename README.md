XP Language
===========
[![Build Status](https://secure.travis-ci.org/xp-framework/xp-language.png)](http://travis-ci.org/xp-framework/xp-language)

This is the XP Language's development checkout

Installation
------------
Clone this repository, e.g. using Git Read-Only:

```sh
$ cd [path]
$ git clone git://github.com/xp-framework/xp-language.git
```

Then add that path to your `use` setting inside your `xp.ini`, e.g.:

```ini
use=~/devel/xp-framework/core:~/devel/xp-language/compiler
```

*On Windows systems, use `;` as separator*

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

Using it
--------
To use the the XP Compiler append the following to the "use" key of your 
xp.ini file:

	# Windows
	;[root]/compiler

	# Un*x
	:[root]/compiler


Enjoy!
