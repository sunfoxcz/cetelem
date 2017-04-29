Cetelem library
===============

[![Build Status](https://travis-ci.org/sunfoxcz/cetelem.svg?branch=master)](https://travis-ci.org/sunfoxcz/cetelem)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunfoxcz/cetelem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sunfoxcz/cetelem/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sunfoxcz/cetelem/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sunfoxcz/cetelem/?branch=master)

Knihovna pro zjednodušení práce s Webkalkulačkou firmy Cetelem. Prozatím koncipována pro framework Nette.

Instalace
---------

	composer require sunfoxcz/cetelem:~0.4

Použití
-------

```php
use Sunfox\Cetelem\Cetelem;

$storage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/tmp');
$client = new GuzzleHttp\Client;
$cetelem = new Cetelem(2044576, $client, $storage);
$cetelem->setDebug(TRUE);

// Pokud nepoužíváte v Nette, je dobré dát cache i AnnotationsParseru
\Nette\Reflection\AnnotationsParser::setCacheStorage($storage);

$uver = new Cetelem\CetelemUver;
$uver->kodBaremu = '102';
$uver->kodPojisteni = 'A3';
$uver->cenaZbozi = 12000;
$uver->pocetSplatek = 12;
$uver->primaPlatba = 2000;
$uver->odklad = 2;

$cetelem->calculate($uver);
print_r($uver);
```
