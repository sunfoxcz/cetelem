<?php

/**
 * Copyright (c) 2014 Tomas Jacik (tomas.jacik@sunfox.cz)
 */

namespace Sunfox\Cetelem;

use Nette;


/**
 * Communicates with Cetelem Webkalkulator application.
 *
 * @author Tomas Jacik
 */
final class CetelemUver extends Nette\Object
{
	/**
	 * @var string
	 *
	 * Povinný vstup
	 * V testovacím režimu používejte testovacího prodejce číslo 2044576
	 */
	public $kodProdejce;

	/**
	 * @var string
	 *
	 * Povinný vstup
	 * Nutné zobrazovat podle webčíselníku
	 */
	public $kodBaremu;

	/**
	 * @var string
	 *
	 * Povinný vstup
	 * Nutné zobrazovat podle webčíselníku
	 */
	public $kodPojisteni;

	/**
	 * @var string
	 *
	 * Povinný vstup
	 * Nutné zobrazovat ve formátu: Cena zboží: <cenaZbozi> Kč
	 */
	public $cenaZbozi;

	/**
	 * @var string
	 *
	 * Volitelný / modifikovatelný vstup
	 * Nutné zobrazovat ve formátu: Přímá platba: <primaPlatba> Kč
	 */
	public $primaPlatba;

	/**
	 * @var string
	 *
	 * Volitelný / modifikovatelný vstup
	 * Nutné zobrazovat ve formátu: Celková výše úvěru: <vyseUveru> Kč
	 */
	public $vyseUveru;

	/**
	 * @var string
	 *
	 * Povinný vstup
	 * Nutné zobrazovat ve formátu: Počet měsíčních splátek: <pocetSplatek>
	 */
	public $pocetSplatek;

	/**
	 * @var string
	 *
	 * Volitelný / modifikovatelný vstup
	 * Pokud je > 0, zobrazovat ve formátu: Odklad splátky (počet měsíců): <odklad>
	 */
	public $odklad;

	/**
	 * @var string
	 *
	 * Volitelný / modifikovatelný vstup
	 * Nutné zobrazovat ve formátu: Celková výše měsíční splátky (včetně pojištění, má-li být sjednáno): <vyseSplatky> Kč
	 * Musí být zadaná na vstupu, pokud není na vstupu zadán pocetSplatek.
	 */
	public $vyseSplatky;

	/**
	 * @var string
	 *
	 * Pouze výstup
	 * Nutné zobrazovat ve formátu: Cena úvěru (včetně pojištění, má-li být sjednáno): <cenaUveru> Kč
	 */
	public $cenaUveru;

	/**
	 * @var float
	 *
	 * Pouze výstup
	 * Nutné zobrazovat ve formátu: RPSN: <RPSN> % (zobrazení na dvě desetinná místa)
	 */
	public $RPSN;

	/**
	 * @var float
	 *
	 * Pouze výstup
	 * Nutné zobrazovat ve formátu: Roční úroková sazba: <ursaz> % (zobrazení na dvě desetinná místa)
	 */
	public $ursaz;

	/**
	 * @var string
	 *
	 * Pouze výstup
	 * Nutné zobrazovat ve formátu: Za úvěr celkem zaplatíte: <celkovaCastka> Kč
	 */
	public $celkovaCastka;

}
