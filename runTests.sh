#!/bin/bash

composer dump-autoload
vendor/bin/tester tests -s -c tests/php.ini
