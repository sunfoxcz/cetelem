<?php

/**
 * Test: Sunfox\Cetelem\Cetelem basic test.
 */

use Sunfox\Cetelem\Cetelem,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$cetelem = new Cetelem(2044576);
	$cetelem->setDebug(TRUE);

	Assert::same(array(
			'A3' => array('kod' => 'A3', 'nazev' => 'SOUBOR STANDARD'),
			'B1' => array('kod' => 'B1', 'nazev' => 'SOUBOR PREMIUM')
		),
		$cetelem->getPojisteni()
	);
});
