<?php

/**
 * Copyright (c) 2014 Tomas Jacik (tomas.jacik@sunfox.cz)
 */

namespace Sunfox\Cetelem;

use GuzzleHttp;
use Nette;


/**
 * Communicates with Cetelem Webkalkulator application.
 *
 * @author Tomas Jacik
 */
class Cetelem extends Nette\Object
{
	/** @var string */
	private $kodProdejce;

	/** @var GuzzleHttp\ClientInterface */
	private $httpClient;

	/** @var Nette\Caching\Cache */
	private $cache;

	/** @var bool */
	private $debug = FALSE;

	/** @var string */
	static private $devPort = '8654';

	/** @var array */
	static private $urls = [
		'zadost'      => 'https://www.cetelem.cz/cetelem2_webshop.php/zadost-o-pujcku/on-line-zadost-o-pujcku',
		'kalkulacka'  => 'https://www.cetelem.cz/webkalkulacka.php',
		'bareminfo'   => 'https://www.cetelem.cz/bareminfo.php',
		'webciselnik' => 'https://www.cetelem.cz/webciselnik.php'
	];


	/**
	 * @param string $kodProdejce Kod prodejce poskytnuty spolecnosti Cetelem.
	 *        Pro testovaci ucely lze pouzit kod 2044576.
	 * @param GuzzleHttp\ClientInterface $httpClient Knihovna http klienta.
	 * @param Nette\Caching\IStorage $storage Nette storage (napr. FileStorage)
	 *        pro cachovani stahnutych a zparsovanych XML souboru.
	 */
	public function __construct($kodProdejce, GuzzleHttp\ClientInterface $httpClient,
								Nette\Caching\IStorage $storage = NULL)
	{
		$this->kodProdejce = $kodProdejce;
		$this->httpClient = $httpClient;

		if (!$storage) {
			$storage = new Nette\Caching\Storages\MemoryStorage;
		}

		$this->cache = new Nette\Caching\Cache($storage, 'Sunfox.Cetelem.XML');
	}

	/**
	 * @param bool $enabled Povoli ci zakaze dotazy na testovaci server Cetelem.
	 */
	public function setDebug($enabled)
	{
		$this->debug = $enabled;
	}

	/**
	 * Vraci pole s informacemi o typu uveru.
	 *
	 * @return array
	 */
	public function getBarem()
	{
		$xml = $this->parseXml($this->getUrl('bareminfo'));

		$error = $xml->xpath('/bareminfo/chyba');
		if (count($error)) {
			throw new XMLResponseException((string)$error[0]);
		}

		$result = [];
		foreach ($xml->xpath('/bareminfo/barem') as $row)
		{
			$info = [];
			foreach ($row->info as $line)
			{
				$info[] = trim((string)$line);
			}
			$result[(string)$row['id']] = [
				'kod'   => (string)$row['id'],
				'nazev' => trim($row->titul),
				'info'  => implode("\n", $info)
			];
		}

		return $result;
	}

	/**
	 * Vraci pole s informacemi o druhu pojisteni.
	 *
	 * @return array
	 */
	public function getPojisteni()
	{
		$xml = $this->parseXml($this->getUrl('webciselnik') . '&typ=pojisteni');

		$error = $xml->xpath('/webciselnik/chyba');
		if (count($error)) {
			throw new XMLResponseException((string)$error[0]);
		}

		$result = [];
		foreach ($xml->xpath('/webciselnik/moznost') as $row)
		{
			$result[(string)$row['hodnota']] = [
				'kod'   => (string)$row['hodnota'],
				'nazev' => trim((string)$row)
			];
		}

		return $result;
	}

	/**
	 * @param CetelemUver $uver Instance objektu nesouciho informace o uveru
	 *
	 * @return array
	 */
	public function calculate(CetelemUver $uver)
	{
		$xml = $this->parseXml($this->getUrl('kalkulacka') . '&' . http_build_query($uver));

		$info = $xml->xpath('/webkalkulator/info/zprava');
		if (count($info)) {
			foreach ($info as $message) {
				$uver->info[] = (string)$message;
			}
		}

		$status = $xml->xpath('/webkalkulator/status');
		if ((string)$status[0] == 'error') {
			if (count($info)) {
				throw new XMLResponseException((string)$info[0]);
			}

			throw new XMLResponseException('Unknown error');
		}

		$result = $xml->xpath('/webkalkulator/vysledek');
		foreach ($result[0] as $k => $v)
		{
			if (property_exists($uver, $k)) {
				$this->convertType($uver, $k, $v);
			} else {
				throw new CetelemResponseException(
					'Unexpected property ' . $k . ' in Webkalkulator output.'
				);
			}
		}
	}

	/**
	 * @param \Sunfox\Cetelem\CetelemUver $class Instance tridy CetelemUver.
	 * @param string $property Nazev property tridy CetelemUver.
	 * @param mixed $value Hodnota pro nasetovani do property.
	 */
	private function convertType($class, $property, $value)
	{
		$type = Nette\Reflection\ClassType::from($class)
			->getProperty($property)
			->getAnnotation('var');

		if ($type == 'int') {
			$class->$property = (int)(string)$value;
		} elseif ($type == 'float') {
			$class->$property = (float)str_replace(',', '.', (string)$value);
		} else {
			$class->$property = (string)$value;
		}
	}

	/**
	 * @param string $type Sluzba, jejiz url chceme.
	 *
	 * @return string Url sluzby v zavislosti na zapnutem ci vypnutem debug modu.
	 */
	private function getUrl($type)
	{
		$url = self::$urls[$type] . '?kodProdejce=' . $this->kodProdejce;

		if ($this->debug) {
			return str_replace('cetelem.cz', 'cetelem.cz:' . self::$devPort, $url);
		}

		return $url;
	}

	/**
	 * @param string $url Url xml souboru, ktery chceme stahnout.
	 *
	 * @return string Stazeny xml soubor zkonvertovany na kodovani utf-8.
	 */
	private function downloadXml($url)
	{
		$response = $this->httpClient->get($url);

		$xml = $response->getBody();
		$xml = iconv("windows-1250", "utf-8", $xml);
		$xml = str_replace('encoding="windows-1250"', 'encoding="utf-8"', $xml);

		return $xml;
	}

	/**
	 * Funkce zajisti stazeni xml souboru, pokud neni v cache a jeho ulozeni do cache.
	 *
	 * @param string $url Url xml souboru, ktery chceme parsovat.
	 *
	 * @return SimpleXMLElement Instance tridy SimpleXMLElement
	 */
	private function parseXml($url)
	{
		$key = crc32($url);
		$xml = $this->cache->load($key);

		if (!$xml)
		{
			$xml = $this->downloadXml($url);

			$this->cache->save($key, $xml, [
				Nette\Caching\Cache::EXPIRE => '1 day',
			]);
		}

		$previousState = libxml_use_internal_errors(true);
		$xml = simplexml_load_string($xml);
		$this->handleXmlErrors($previousState);
		libxml_use_internal_errors($previousState);

		return $xml;
	}

	/**
	 * Vyporada se s chybami pri parsovani xml, pokud narazi na chybu, vyhodi vyjimku.
	 *
	 * @param bool $previousState Predchozi stav nastaveni pro libxml_use_internal_errors.
	 */
	private function handleXmlErrors($previousState)
	{
		if (count(libxml_get_errors()))
		{
			$error = NULL;
			foreach (libxml_get_errors() as $e) {
				$error = $e;
			}

			libxml_clear_errors();
			libxml_use_internal_errors($previousState);

			// TODO: Zobrazit vsechny chyby v XML
			throw new XMLParserException($error->message, $error->code);
		}
	}

}
