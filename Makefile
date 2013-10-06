##
# Makefile to generate parsers from grammar files

GEN?=../jay

all: src/main/php/xp/compiler/syntax/xp/Parser.class.php src/main/php/xp/compiler/syntax/php/Parser.class.php

clean:
	-rm y.output

src/main/php/xp/compiler/syntax/xp/Parser.class.php: src/main/jay/grammars/xp.jay $(GEN)/skel/php5-ns.skl
	sh $(GEN)/generate.sh src/main/jay/grammars/xp.jay php5-ns > $@

src/main/php/xp/compiler/syntax/php/Parser.class.php: src/main/jay/grammars/php.jay $(GEN)/skel/php5-ns.skl
	sh $(GEN)/generate.sh src/main/jay/grammars/php.jay php5-ns > $@

