#!/bin/sh
# $Id$

PHP=/usr/local/bin/php
PERL=/usr/bin/perl
XMLLINT=/usr/local/bin/xmllint

EXTENSION=`echo $1 | sed -E 's/.+\.([a-z]+)$/\1/g'`

case $EXTENSION in
  php)
    $PHP -l $1
    ;;
  
  pl)
    $PERL -w -c $1
    ;;

  xml | xsl)
    $XMLLINT --noout $1 && echo `basename $1`" syntax OK"
    ;;

  *)
    echo "No syntax checker available for $EXTENSION"
    exit 1
    ;;

esac

exit $?
