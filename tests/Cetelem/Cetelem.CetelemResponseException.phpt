<?php

/**
 * Test: Sunfox\Cetelem\Cetelem calculate test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\GuzzleHttp\Psr7\Response');
	$builder->getBody()->once()->andReturn(
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
	$responsetMock = $builder->getMock();

	$builder = $mockista->createBuilder('\GuzzleHttp\Client');
	$clientMock = $builder->getMock();
	$clientMock->expects('get')->once()->andReturn($responsetMock);

	$cetelem = new Cetelem\Cetelem(KOD_PRODEJCE, $clientMock);
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
