<?php

/**
 * Test: Sunfox\Cetelem\Cetelem XMLParserException test.
 */

use Sunfox\Cetelem;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
    $responsetMock = Mockery::mock('GuzzleHttp\Psr7\Response')
        ->shouldReceive('getBody')->once()
        ->andReturn('<bad><xml>')
        ->getMock();

    $clientMock = Mockery::mock('GuzzleHttp\Client')
        ->shouldReceive('get')->once()
        ->andReturn($responsetMock)
        ->getMock();

	$cetelem = new Cetelem\Cetelem(KOD_PRODEJCE, $clientMock);
	$cetelem->setDebug(TRUE);

	Assert::exception(
		function () use ($cetelem) { $cetelem->getBarem(); },
		'Sunfox\Cetelem\XMLParserException'
	);

    Mockery::close();
});
