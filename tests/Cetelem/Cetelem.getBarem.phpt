<?php

/**
 * Test: Sunfox\Cetelem\Cetelem getBarem test.
 */

use Sunfox\Cetelem,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function() {
	$mockista = new \Mockista\Registry();

	$builder = $mockista->createBuilder('\Kdyby\Curl\Response');
	$builder->getResponse()->once()->andReturn(file_get_contents(__DIR__ . '/../sample_barem.xml'));
	$resultMock = $builder->getMock();

	$builder = $mockista->createBuilder('\Kdyby\Curl\CurlSender');
	$curlSenderMock = $builder->getMock();
	$curlSenderMock->expects('send')->once()->andReturn($resultMock);

	$cetelem = new Cetelem\Cetelem('2044576', $curlSenderMock);
	$cetelem->setDebug(TRUE);

	Assert::same(
		$cetelem->getBarem(),
		array(
			210 => array(
				'kod' => '210',
				'nazev' => '0%+24x5 - zdarma 10',
				'info' => "úvěr 2500,- až 400000,- Kč\nvolitelná přímá platba od 0%\npočet splátek 24"
			),
			227 => array(
				'kod' => '227',
				'nazev' => '36ms+1ms - zdarma 12',
				'info' => "úvěr 2500,- až 400000,- Kč\nvolitelná přímá platba od 0%\npočet splátek 36\nmožný odklad splátek 2 měsíce"
			),
			228 => array(
				'kod' => '228',
				'nazev' => '0%+15ms+odklad - zdarma 4',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba 0,- Kč\npočet splátek 15\nmožný odklad splátek 2 až 5 měsíců"
			),
			229 => array(
				'kod' => '229',
				'nazev' => '15ms - zdarma 8',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba vždy 10% z ceny zboží\npočet splátek 15"
			),
			174 => array(
				'kod' => '174',
				'nazev' => 'Zdarma variabilně',
				'info' => "úvěr 2500,- až 200000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 4 do 20"
			),
			100 => array(
				'kod' => '100',
				'nazev' => 'Klasický úvěr',
				'info' => "úvěr 2500,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 5 do 48\nmožný odklad splátek 2 až 5 měsíců"
			),
			102 => array(
				'kod' => '102',
				'nazev' => 'Klasický úvěr',
				'info' => "úvěr 2500,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 5 do 48\nmožný odklad splátek 2 až 5 měsíců"
			),
			104 => array(
				'kod' => '104',
				'nazev' => '10% + 10 x 10%',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba vždy 10% z ceny zboží\npočet splátek 10"
			),
			160 => array(
				'kod' => '160',
				'nazev' => 'BUĎ A NEBO - zdarma do 2 měsíců',
				'info' => "úvěr 2500,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 5 do 36\nmožný odklad splátek 3 měsíce"
			),
			623 => array(
				'kod' => '623',
				'nazev' => 'Bez navýšení 10% + 9 x 10%',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba vždy 10% z ceny zboží\npočet splátek 9"
			),
			201 => array(
				'kod' => '201',
				'nazev' => '1% měsíčně',
				'info' => "úvěr 2500,- až 200000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 5 do 25"
			),
			235 => array(
				'kod' => '235',
				'nazev' => 'Klasický úvěr',
				'info' => "úvěr 2500,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 6 do 36\nmožný odklad splátek 2 až 5 měsíců"
			),
			216 => array(
				'kod' => '216',
				'nazev' => 'Zdarma do 6 měsíců, jinak 20 měs.',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba 0,- Kč\npočet splátek 20"
			),
			221 => array(
				'kod' => '221',
				'nazev' => 'Zdarma do 6 měsíců, jinak 36 měs.',
				'info' => "úvěr 2500,- až 200000,- Kč\nvolitelná přímá platba od 0%\npočet splátek 36"
			),
			222 => array(
				'kod' => '222',
				'nazev' => 'Zdarma do 9 měsíců, jinak 36 měs.',
				'info' => "úvěr 2500,- až 200000,- Kč\nvolitelná přímá platba od 0%\npočet splátek 36"
			),
			223 => array(
				'kod' => '223',
				'nazev' => '36ms - zdarma 12',
				'info' => "úvěr 2500,- až 400000,- Kč\nvolitelná přímá platba od 0%\npočet splátek 36"
			),
			226 => array(
				'kod' => '226',
				'nazev' => 'Zdarma do 4 měsíců, jinak 10%+10x10',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba vždy 10% z ceny zboží\npočet splátek 10"
			),
			151 => array(
				'kod' => '151',
				'nazev' => '10%+11x10 - zdarma 2',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba vždy 10% z ceny zboží\npočet splátek 11\nmožný odklad splátek 3 měsíce"
			),
			613 => array(
				'kod' => '613',
				'nazev' => 'IKEA Klasik OSVČ',
				'info' => "úvěr 10000,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 12 do 48"
			),
			614 => array(
				'kod' => '614',
				'nazev' => 'IKEA Klasik PRÁVNICKÁ OSOBA',
				'info' => "úvěr 20000,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 12 do 48"
			),
			303 => array(
				'kod' => '303',
				'nazev' => 'Klasický úvěr 10-14',
				'info' => "úvěr 2500,- až 200000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 10 do 14\nmožný odklad splátek 2 až 5 měsíců"
			),
			945 => array(
				'kod' => '945',
				'nazev' => '0% + 10 x 10% (odklad)',
				'info' => "úvěr 2500,- až 400000,- Kč\npřímá platba 0,- Kč\npočet splátek 10\nmožný odklad splátek 2 až 5 měsíců"
			),
			938 => array(
				'kod' => '938',
				'nazev' => 'Variabilní úvěr ALZA.CZ',
				'info' => "úvěr 2500,- až 200000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 10 do 48\nmožný odklad splátek 2 až 5 měsíců"
			),
			610 => array(
				'kod' => '610',
				'nazev' => '1 % měsíčně (5 - 30 MS)',
				'info' => "úvěr 2500,- až 200000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 5 do 30"
			),
			615 => array(
				'kod' => '615',
				'nazev' => 'ALZA Klasik OSVČ',
				'info' => "úvěr 10000,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 12 do 48"
			),
			616 => array(
				'kod' => '616',
				'nazev' => 'ALZA Klasik PRÁVNICKÁ OSOBA',
				'info' => "úvěr 20000,- až 400000,- Kč\nvolitelná přímá platba od 0%\nvolitelný počet splátek od 12 do 48"
			)
		)
	);

	$mockista->assertExpectations();
});
