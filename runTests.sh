#!/bin/bash

composer dump-autoload
vendor/bin/tester tests -s -p php -c tests/php.ini
