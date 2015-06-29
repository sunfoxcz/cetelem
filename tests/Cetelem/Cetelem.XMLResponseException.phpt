<?php

/**
 * Test: Sunfox\Cetelem\Cetelem XMLResponseException test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\GuzzleHttp\Psr7\Response');
	$builder->getBody()->once()->andReturn(
		'<bareminfo><chyba>Error</chyba></bareminfo>'
	);
	$responsetMock = $builder->getMock();

	$builder = $mockista->createBuilder('\GuzzleHttp\Client');
	$clientMock = $builder->getMock();
	$clientMock->expects('get')->once()->andReturn($responsetMock);

	$cetelem = new Cetelem\Cetelem(KOD_PRODEJCE, $clientMock);
	$cetelem->setDebug(TRUE);

	Assert::exception(
		function () use ($cetelem) { $cetelem->getBarem(); },
		'Sunfox\Cetelem\XMLResponseException', 'Error'
	);

	$mockista->assertExpectations();
});
