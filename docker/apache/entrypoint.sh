#!/bin/sh

composer install

npm install -D

npx tailwindcss -i ./src/App/input.css -o ./dist/tailwind.css

ln -f ./node_modules/flowbite/dist/flowbite.min.js ./dist/

apache2ctl -D FOREGROUND
