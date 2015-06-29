<?php

/**
 * Test: Sunfox\Cetelem\Cetelem getPojisteni test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\GuzzleHttp\Psr7\Response');
	$builder->getBody()->once()->andReturn(
		file_get_contents(__DIR__ . '/../sample_pojisteni.xml')
	);
	$responsetMock = $builder->getMock();

	$builder = $mockista->createBuilder('\GuzzleHttp\Client');
	$clientMock = $builder->getMock();
	$clientMock->expects('get')->once()->andReturn($responsetMock);

	$cetelem = new Cetelem\Cetelem(KOD_PRODEJCE, $clientMock);
	$cetelem->setDebug(TRUE);

	Assert::same(
		$cetelem->getPojisteni(), [
			'A3' => ['kod' => 'A3', 'nazev' => 'SOUBOR STANDARD'],
			'B1' => ['kod' => 'B1', 'nazev' => 'SOUBOR PREMIUM'],
		]
	);

	$mockista->assertExpectations();
});
