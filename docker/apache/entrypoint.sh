#!/bin/sh

composer install

npm install -D

./build.sh

ln ./node_modules/flowbite/dist/flowbite.min.js ./dist/

apache2ctl -D FOREGROUND
