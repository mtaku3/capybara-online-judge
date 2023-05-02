#!/bin/sh

composer install

npm install -D

./build.sh

apache2ctl -D FOREGROUND
