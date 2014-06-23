<?php

/**
 * Test: Sunfox\Cetelem\Cetelem getPojisteni test.
 */

use Sunfox\Cetelem,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();
	$builder = $mockista->createBuilder('\Kdyby\Curl\CurlWrapper');
	$builder->setUrl('https://www.cetelem.cz:8654/webciselnik.php?kodProdejce=2044576&typ=pojisteni')->once();
	$builder->execute()->once();

	$curlWrapperMock = $builder->getMock();
	$curlWrapperMock->info = array('http_code' => 200);
	$curlWrapperMock->response = file_get_contents(__DIR__ . '/../sample_pojisteni.xml');

	$cetelem = new Cetelem\Cetelem('2044576', $curlWrapperMock);
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
