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
Add that path to your `use` setting inside your `xp.ini`, e.g.:

```ini
use=~/devel/xp-framework/core:.:~/devel/xp-language/compiler
                                ^^^^^^^^^^^^^^^^^^^^^^^^^^^^
```

*On Windows systems, use `;` as separator*

**Enjoy!**

Contributing
------------
To contribute, use the GitHub way - fork, hack, and submit a pull request!
