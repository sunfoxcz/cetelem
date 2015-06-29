<?php

/**
 * Test: Sunfox\Cetelem\Cetelem getBarem test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\Kdyby\Curl\Response');
	$builder->getResponse()->once()->andReturn(
		file_get_contents(__DIR__ . '/../sample_barem.xml')
	);
	$resultMock = $builder->getMock();

	$builder = $mockista->createBuilder('\Kdyby\Curl\CurlSender');
	$curlSenderMock = $builder->getMock();
	$curlSenderMock->expects('send')->once()->andReturn($resultMock);

	$cetelem = new Cetelem\Cetelem('2044576', $curlSenderMock);
	$cetelem->setDebug(TRUE);

	Assert::same(
		$cetelem->getBarem(),
		include __DIR__ . '/../sample_barem.php'
	);

	$mockista->assertExpectations();
});
