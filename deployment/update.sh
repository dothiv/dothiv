#!/bin/bash

ENV=$1

V=`date +%s`; sed -i -r -e "s/(\W+)assets_version:(\W+)[^\n]+/\1assets_version:\2$V/" app/config/parameters.yml

composer install
npm install

app/console --env=$ENV assets:install --symlink
app/console --env=$ENV assetic:dump
app/console --env=$ENV cache:clear
