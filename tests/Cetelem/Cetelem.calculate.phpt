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
		file_get_contents(__DIR__ . '/../sample_calc.xml')
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

	$cetelem->calculate($uver);

	Assert::same($uver->kodBaremu, '102');
	Assert::same($uver->kodPojisteni, 'A3');
	Assert::same($uver->cenaZbozi, 12000);
	Assert::same($uver->primaPlatba, 2000);
	Assert::same($uver->vyseUveru, 10000);
	Assert::same($uver->pocetSplatek, 12);
	Assert::same($uver->odklad, 2);
	Assert::same($uver->vyseSplatky, 1046);
	Assert::same($uver->cenaUveru, 2552);
	Assert::same($uver->RPSN, 45.19);
	Assert::same($uver->ursaz, 32.34);
	Assert::same($uver->celkovaCastka, 12552);
	Assert::same($uver->info, ['Prodejce nema povolen produkt']);

	$mockista->assertExpectations();
});
