<?php

/**
 * Test: Sunfox\Cetelem\Cetelem XMLParserException test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\GuzzleHttp\Psr7\Response');
	$builder->getBody()->once()->andReturn('<bad><xml>');
	$responsetMock = $builder->getMock();

	$builder = $mockista->createBuilder('\GuzzleHttp\Client');
	$clientMock = $builder->getMock();
	$clientMock->expects('get')->once()->andReturn($responsetMock);

	$cetelem = new Cetelem\Cetelem(KOD_PRODEJCE, $clientMock);
	$cetelem->setDebug(TRUE);

	Assert::exception(
		function () use ($cetelem) { $cetelem->getBarem(); },
		'Sunfox\Cetelem\XMLParserException'
	);

	$mockista->assertExpectations();
});
