<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Cetelem/Cetelem.php';
require __DIR__ . '/../src/Cetelem/CetelemUver.php';
require __DIR__ . '/../src/Cetelem/exceptions.php';

use Sunfox\Cetelem;


// @mkdir(__DIR__ . '/tmp');
// $storage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/tmp');
$storage = new Nette\Caching\Storages\MemoryStorage;

Nette\Reflection\AnnotationsParser::setCacheStorage($storage);

$curl = new Kdyby\Curl\CurlSender;

$cetelem = new Cetelem\Cetelem('2044576', $curl, $storage);
$cetelem->setDebug(TRUE);


echo "------------------------------------------------------------\n";
echo "UVERY:\n";
echo "------------------------------------------------------------\n";
foreach ($cetelem->barem as $row)
{
	foreach ($row as $k => $v)
	{
		echo $k . ': ' . $v . "\n";
	}
}

echo "------------------------------------------------------------\n";
echo "POJISTENI:\n";
echo "------------------------------------------------------------\n";
foreach ($cetelem->pojisteni as $row)
{
	foreach ($row as $k => $v)
	{
		echo $k . ': ' . $v . "\n";
	}
}

echo "------------------------------------------------------------\n";
echo "UKAZKOVY UVER:\n";
echo "------------------------------------------------------------\n";
$uver = new Cetelem\CetelemUver;
$uver->kodBaremu = '102';
$uver->kodPojisteni = 'A3';
$uver->cenaZbozi = 12000;
$uver->pocetSplatek = 12;
$uver->primaPlatba = 2000;
$uver->odklad = 2;

$cetelem->calculate($uver);
print_r($uver);
