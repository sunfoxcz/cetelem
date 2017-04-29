<?php

/**
 * Test: Sunfox\Cetelem\Cetelem getPojisteni test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
    $responsetMock = Mockery::mock('GuzzleHttp\Psr7\Response')
        ->shouldReceive('getBody')->once()
        ->andReturn(file_get_contents(__DIR__ . '/../samples/sample_pojisteni.xml'))
        ->getMock();

    $clientMock = Mockery::mock('GuzzleHttp\Client')
        ->shouldReceive('get')->once()
        ->andReturn($responsetMock)
        ->getMock();

	$cetelem = new Cetelem\Cetelem(KOD_PRODEJCE, $clientMock);
	$cetelem->setDebug(TRUE);

	Assert::same(
		$cetelem->getPojisteni(), [
			'A3' => ['kod' => 'A3', 'nazev' => 'SOUBOR STANDARD'],
			'B1' => ['kod' => 'B1', 'nazev' => 'SOUBOR PREMIUM'],
		]
	);

    Mockery::close();
});
