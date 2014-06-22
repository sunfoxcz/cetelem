<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Cetelem/Cetelem.php';

use Sunfox\Cetelem\Cetelem;


@mkdir(__DIR__ . '/tmp');
$storage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/tmp');
$cetelem = new Cetelem(2044576, $storage);
$cetelem->setDebug(TRUE);


echo "------------------------------------------------------------\n";
echo "UVERY:\n";
echo "------------------------------------------------------------\n";
print_r($cetelem->barem);
exit;
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
echo "SORTIMENT:\n";
echo "------------------------------------------------------------\n";
foreach ($cetelem->material as $row)
{
	foreach ($row as $k => $v)
	{
		echo $k . ': ' . $v . "\n";
	}
}

echo "------------------------------------------------------------\n";
echo "UKAZKOVY UVER:\n";
echo "------------------------------------------------------------\n";
$uver = $cetelem->calculate(
		102,   // kod typu uveru
		'A3',  // kod druhu pojisteni
		758,   // kod druhu sortimentu
		12000, // cena zbozi
		0,     // prima platba
		12,    // pocet splatek
		0      // odklad
	);
foreach ($uver as $k => $v)
{
	echo $k . ': ' . $v . "\n";
}
