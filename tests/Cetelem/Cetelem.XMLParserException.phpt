<?php

/**
 * Test: Sunfox\Cetelem\Cetelem XMLParserException test.
 */

use Sunfox\Cetelem,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\Kdyby\Curl\Response');
	$builder->getResponse()->once()->andReturn(
		'<bad><xml>'
	);
	$resultMock = $builder->getMock();

	$builder = $mockista->createBuilder('\Kdyby\Curl\CurlSender');
	$curlSenderMock = $builder->getMock();
	$curlSenderMock->expects('send')->once()->andReturn($resultMock);

	$cetelem = new Cetelem\Cetelem('2044576', $curlSenderMock);
	$cetelem->setDebug(TRUE);

	Assert::exception(
		function() use ($cetelem) { $cetelem->getBarem(); },
		'Sunfox\Cetelem\XMLParserException'
	);

	$mockista->assertExpectations();
});
