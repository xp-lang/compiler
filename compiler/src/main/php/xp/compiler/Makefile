##
# Makefile to generate parsers from grammar files
#
# $Id$

GEN?=../../../ports/technologies/opt/jay

all: syntax/xp/Parser.class.php syntax/php/Parser.class.php

clean:
	-rm y.output

syntax/xp/Parser.class.php: grammar/xp.jay $(GEN)/skel/php5.skl
	sh $(GEN)/generate.sh grammar/xp.jay php5-ns > $@

syntax/php/Parser.class.php: grammar/php.jay $(GEN)/skel/php5.skl
	sh $(GEN)/generate.sh grammar/php.jay php5-ns > $@

