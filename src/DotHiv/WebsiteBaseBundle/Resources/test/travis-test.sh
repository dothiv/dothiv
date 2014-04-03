#!/bin/bash

BASE_DIR=`dirname $0`

echo ""
echo "Starting Karma Server (http://karma-runner.github.io)"
echo "-------------------------------------------------------------------"

KARMA_BIN=${KARMA_BIN:=karma}
$KARMA_BIN start $BASE_DIR/config/travis-karma.conf.js $*
