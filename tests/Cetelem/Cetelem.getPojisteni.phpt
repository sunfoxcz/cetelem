<?php

/**
 * Test: Sunfox\Cetelem\Cetelem getPojisteni test.
 */

use Sunfox\Cetelem,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\Kdyby\Curl\Response');
	$builder->getResponse()->once()->andReturn(file_get_contents(__DIR__ . '/../sample_pojisteni.xml'));
	$resultMock = $builder->getMock();

	$builder = $mockista->createBuilder('\Kdyby\Curl\CurlSender');
	$curlSenderMock = $builder->getMock();
	$curlSenderMock->expects('send')->once()->andReturn($resultMock);

	$cetelem = new Cetelem\Cetelem('2044576', $curlSenderMock);
	$cetelem->setDebug(TRUE);

	Assert::same(
		$cetelem->getPojisteni(),
		array(
			'A3' => array('kod' => 'A3', 'nazev' => 'SOUBOR STANDARD'),
			'B1' => array('kod' => 'B1', 'nazev' => 'SOUBOR PREMIUM'),
		)
	);

	$mockista->assertExpectations();
});
