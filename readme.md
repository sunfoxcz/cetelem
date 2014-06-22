Cetelem library
===============

[![Build Status](https://travis-ci.org/sunfoxcz/cetelem.svg?branch=master)](https://travis-ci.org/sunfoxcz/cetelem)

Knihovna pro zjednodušení práce s Webkalkulačkou firmy Cetelem. Prozatím koncipována pro framework Nette.

Instalace
---------

	composer require sunfoxcz/cetelem:~0.1

Použití
-------

```php
use Sunfox\Cetelem\Cetelem;

$storage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/temp');
$cetelem = new Cetelem(2126712, $storage);
```
