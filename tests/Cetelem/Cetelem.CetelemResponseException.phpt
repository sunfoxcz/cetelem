<?php

/**
 * Test: Sunfox\Cetelem\Cetelem calculate test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\Kdyby\Curl\Response');
	$builder->getResponse()->once()->andReturn(
		'<webkalkulator>' .
			'<status>ok</status>' .
			'<vysledek>' .
				'<kodProdejce>2044576</kodProdejce>' .
				'<kodBaremu>102</kodBaremu>' .
				'<kodPojisteni>A3</kodPojisteni>' .
				'<cenaZbozi>12000</cenaZbozi>' .
				'<primaPlatba>2000</primaPlatba>' .
				'<vyseUveru>10000</vyseUveru>' .
				'<pocetSplatek>12</pocetSplatek>' .
				'<odklad>2</odklad>' .
				'<vyseSplatky>1046</vyseSplatky>' .
				'<cenaUveru>2552</cenaUveru>' .
				'<RPSN>45,19</RPSN>' .
				'<ursaz>32,34</ursaz>' .
				'<celkovaCastka>12552</celkovaCastka>' .
				'<test>abc</test>' .
			'</vysledek>' .
		'</webkalkulator>'
	);
	$resultMock = $builder->getMock();

	$builder = $mockista->createBuilder('\Kdyby\Curl\CurlSender');
	$curlSenderMock = $builder->getMock();
	$curlSenderMock->expects('send')->once()->andReturn($resultMock);

	$cetelem = new Cetelem\Cetelem('2044576', $curlSenderMock);
	$cetelem->setDebug(TRUE);

	$uver = new Cetelem\CetelemUver;
	$uver->kodBaremu = '102';
	$uver->kodPojisteni = 'A3';
	$uver->cenaZbozi = 12000;
	$uver->pocetSplatek = 12;
	$uver->primaPlatba = 2000;
	$uver->odklad = 2;

	Assert::exception(
		function() use ($cetelem, $uver) { $cetelem->calculate($uver); },
		'Sunfox\Cetelem\CetelemResponseException',
		'Unexpected property test in Webkalkulator output.'
	);

	$mockista->assertExpectations();
});
