<?php

/**
 * Test: Sunfox\Cetelem\Cetelem getBarem test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\GuzzleHttp\Psr7\Response');
	$builder->getBody()->once()->andReturn(
		file_get_contents(__DIR__ . '/../sample_barem.xml')
	);
	$responsetMock = $builder->getMock();

	$builder = $mockista->createBuilder('\GuzzleHttp\Client');
	$clientMock = $builder->getMock();
	$clientMock->expects('get')->once()->andReturn($responsetMock);

	$cetelem = new Cetelem\Cetelem(KOD_PRODEJCE, $clientMock);
	$cetelem->setDebug(TRUE);

	Assert::same(
		$cetelem->getBarem(),
		include __DIR__ . '/../sample_barem.php'
	);

	$mockista->assertExpectations();
});
