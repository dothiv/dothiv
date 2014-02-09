#!/bin/bash

# Ask bash to stop if something happens.
set -e

DIR="`dirname $0`/../"
BASEPATH=`readlink -f $DIR`
ENV="prod"
UPDATEFLAG="$BASEPATH/app/cache/update"
UPDATELOCK="$UPDATEFLAG.lock"

if [ "$1" == "-v" ]
  then
    echo "BASEPATH: $BASEPATH"
    echo "UPDATEFLAG: $UPDATEFLAG"
    echo "UPDATELOCK: $UPDATELOCK"
  fi

if [ ! -f $UPDATEFLAG ]
then
  if [ "$1" == "-v" ]
  then
    echo "No update needed."
  fi
  exit -1
fi

if [ -f $UPDATELOCK ]
then
  if [ "$1" == "-v" ]
  then
    echo "Update in progress since `cat $UPDATELOCK`."
  fi
  exit -2
fi

echo `date '+%Y-%m-%d %H:%M:%S'` > $UPDATELOCK

echo "Updating $BASEPATH …"

cd $BASEPATH

ln -sf maintenance.php web/index.php

app/console --env=$ENV cache:clear

V=`date +%s`; sed -i -r -e "s/(\W+)assets_version:(\W+)[^\n]+/\1assets_version:\2$V/" app/config/parameters.yml

/var/lib/jenkins/bin/composer.phar install
npm install

app/console --env=$ENV assets:install --symlink
app/console --env=$ENV assetic:dump

ln -sf app.php web/index.php

rm $UPDATEFLAG
rm $UPDATELOCK