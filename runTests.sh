#!/bin/bash

INI='tests/php.ini'
if php -v | grep -q 'deb.sury.org'; then
    INI='tests/php-ondrej.ini'
fi

composer dump-autoload
vendor/bin/tester tests -s -p php -c $INI
