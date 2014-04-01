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

echo "# Updating $BASEPATH ..."
cd $BASEPATH

echo ""
echo "# Writing lock file ..."
echo $UPDATELOCK
echo `date '+%Y-%m-%d %H:%M:%S'` > $UPDATELOCK

echo ""
echo "# Activating maintenance page ..."
ln -sfv maintenance.php web/index.php

echo ""
echo "# Stashing changes ..."
# We could also do a git reset --hard here...
git stash

echo ""
echo "# Pulling ..."
git pull

echo ""
app/console --env=$ENV cache:clear

echo ""
echo "# composer install ..."
COMPOSER_BIN=${COMPOSER_BIN:=composer.phar}
$COMPOSER_BIN install
echo ""
echo "# npm install ..."
npm install
echo ""

# Scans the src dir and finds the js or css file with the latest modification time
function findLatestSrc {
    echo `find src/ -type f -name \*.css -o -type f -name \*.js -printf '%T@ %p\n' | sort -n | tail -1 | cut -f2- -d" "`
}

# Scans the web dir and finds js or css file with the latest modification time
# It specifically searches for the minified files as the other files may be copies of
# the files in the src folder.
function findLatestMin {
    echo `find web/{css,js} -type f -name \*.css -o -type f -name \*.min.js -printf '%T@ %p\n' | sort -n | tail -1 | cut -f2- -d" "`
}

LATESTSRC=$(findLatestSrc)
LATESTCACHE=$(findLatestMin)

if [ $LATESTSRC -nt $LATESTCACHE ]
then
    echo ""
    V=`date +%s`
    echo "# Updating assets_version to $V"
    sed -i -r -e "s/(\W+)assets_version:(\W+)[^\n]+/\1assets_version:\2$V/" app/config/parameters.yml
    echo "# Updating assets"
    app/console --env=$ENV assets:install --symlink
    app/console --env=$ENV assetic:dump
fi

# Check if there is already a new update.
git fetch origin
# We specifically use the return code of the next command.
set +e
git diff --exit-code --quiet origin/master
if [ $? != 0 ]
then
    echo "# New version exist on origin. Restarting ..."

    # Protect against infinite loops
    LOOPCOUNT=$[$1 +1]
    if [ "$LOOPCOUNT" -lt "1" ]
    then
        echo "Invalid loopcount supplied."
        exit 1
    fi
    if [ "$LOOPCOUNT" -gt "10" ]
    then
        echo "Infinite loop detected."
        exit 1
    fi

    # Remove the lock only. Update will then pickup again.
    rm -v $UPDATELOCK
    # Launch again.
    bash $0 $LOOPCOUNT
else
    echo "# Nothing has changed in the meantime."
fi
set -e

echo ""
echo "# Deactivating maintenance page ..."
ln -sfv app.php web/index.php

echo ""
echo "# Removing update flag and lock file ..."
rm -v $UPDATEFLAG
rm -v $UPDATELOCK

echo ""
echo "# Done."
