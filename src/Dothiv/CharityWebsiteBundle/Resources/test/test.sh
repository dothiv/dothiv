#!/bin/bash

BASE_DIR=`dirname $0`

echo ""
echo "Starting Karma Server (http://karma-runner.github.io)"
echo "-------------------------------------------------------------------"

CHROME_BIN=${CHROME_BIN:=`which chromium-browser`}
KARMA_BIN=${KARMA_BIN:=karma}
$KARMA_BIN start $BASE_DIR/config/karma.conf.js $*
