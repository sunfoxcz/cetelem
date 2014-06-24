<?php

/**
 * Copyright (c) 2014 Tomas Jacik (tomas.jacik@sunfox.cz)
 */

namespace Sunfox\Cetelem;

use Nette,
	Kdyby;


/**
 * Communicates with Cetelem Webkalkulator application.
 *
 * @author Tomas Jacik
 */
class Cetelem extends Nette\Object
{
	/** @var string */
	private $kodProdejce;

	/** @var \Kdyby\Curl\CurlWrapper */
	private $curl;

	/** @var \Nette\Caching\Cache */
	private $cache;

	/** @var bool */
	private $debug = FALSE;

	/* @var string */
	static private $devPort = '8654';

	/** @var array */
	static $urls = array(
		'zadost'      => 'https://www.cetelem.cz/cetelem2_webshop.php/zadost-o-pujcku/on-line-zadost-o-pujcku',
		'kalkulacka'  => 'https://www.cetelem.cz/webkalkulacka.php',
		'bareminfo'   => 'https://www.cetelem.cz/bareminfo.php',
		'webciselnik' => 'https://www.cetelem.cz/webciselnik.php'
	);


	/**
	 * @param string $kodProdejce Kod prodejce poskytnuty spolecnosti Cetelem.
	 *        Pro testovaci ucely lze pouzit kod 2044576.
	 * @param \Nette\Caching\IStorage $storage Nette storage (napr. FileStorage)
	 *        pro cachovani stahnutych a zparsovanych XML souboru.
	 */
	public function __construct($kodProdejce, Kdyby\Curl\CurlWrapper $curl,
								Nette\Caching\IStorage $storage = NULL)
	{
		$this->kodProdejce = $kodProdejce;
		$this->curl = $curl;

		if ($storage)
		{
			$this->cache = new Nette\Caching\Cache($storage, 'Sunfox.Cetelem.XML');
		}
		else
		{
			$this->cache = new Nette\Caching\Cache(
				new Nette\Caching\Storages\MemoryStorage, 'Sunfox.Cetelem.XML'
			);
		}
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
		$xml = $this->parseXml($this->getUrl('bareminfo') . '?kodProdejce=' . $this->kodProdejce);

		$error = $xml->xpath('/bareminfo/chyba');
		if (count($error))
		{
			// TODO: Vlastni exception
			throw new \Exception((string)$error[0]);
		}

		$result = array();
		foreach ($xml->xpath('/bareminfo/barem') as $row)
		{
			$info = array();
			foreach ($row->info as $line)
			{
				$info[] = trim((string)$line);
			}
			$result[(string)$row['id']] = array(
				'kod'   => (string)$row['id'],
				'nazev' => trim($row->titul),
				'info'  => implode("\n", $info)
			);
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
		$xml = $this->parseXml($this->getUrl('webciselnik') . '?kodProdejce=' .
								$this->kodProdejce . '&typ=pojisteni');

		$error = $xml->xpath('/webciselnik/chyba');
		if (count($error))
		{
			// TODO: Vlastni exception
			throw new \Exception((string)$error[0]);
		}

		$result = array();
		foreach ($xml->xpath('/webciselnik/moznost') as $row)
		{
			$result[(string)$row['hodnota']] = array(
				'kod'   => (string)$row['hodnota'],
				'nazev' => trim((string)$row)
			);
		}

		return $result;
	}

	/**
	 * @param int $kodBaremu Kod typu uveru dle dat zikanych funkci getBarem.
	 * @param int $kodPojisteni Kod druhu pojisteni dle dat zikanych funkci getPojisteni.
	 * @param int $cenaZbozi Celkova cena zbozi vcetne DPH.
	 * @param int $pocetSplatek Pocet splatek uveru
	 * @param int $primaPlatba Nepovinne, kolik zaplati zakaznik jeste pred uverem.
	 * @param int $odklad Nepovinne, odklad uveru v mesicich
	 *
	 * @return array
	 */
	public function calculate(CetelemUver $uver)
	{
		$uver->kodProdejce = $this->kodProdejce;

		$xml = $this->parseXml($this->getUrl('kalkulacka') . '?' . http_build_query($uver));

		$status = $xml->xpath('/webkalkulator/status');
		if ((string)$status[0] == 'error')
		{
			$error = $xml->xpath('/webkalkulator/info/zprava');
			// TODO: Vlastni exception
			throw new \Exception((string)$error[0]);
		}

		$result = $xml->xpath('/webkalkulator/vysledek');
		foreach ($result[0] as $k => $v)
		{
			if (property_exists($uver, $k))
			{
				$this->convertType($uver, $k, $v);
			}
			else
			{
				throw new \Exception('Unexpected property ' . $k . ' in Webkalkulator output.');
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
		$type = Nette\Reflection\ClassType::from($class)->getProperty($property)->getAnnotation('var');

		if ($type == 'int')
		{
			$class->$property = (int)(string)$value;
		}
		elseif ($type == 'float')
		{
			$class->$property = (float)str_replace(',', '.', (string)$value);
		}
		else
		{
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
		$url = self::$urls[$type];

		if ($this->debug)
		{
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
		$this->curl->setUrl($url);
		$this->curl->execute();

		if ($this->curl->info['http_code'] != 200)
		{
			// TODO: Vlastni exception
			throw new \Exception('CURL error with HTTP code ' . $this->curl->info['http_code'] .
									' for url: ' . $url);
		}

		$xml = $this->curl->response;
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
		$key = $this->kodProdejce . '_' . crc32($url);
		$xml = $this->cache->load($key);

		if (!$xml)
		{
			$xml = $this->downloadXml($url);

			$this->cache->save($key, $xml, array(
				Nette\Caching\Cache::EXPIRE => '1 day',
			));
		}

		$previousState = libxml_use_internal_errors(true);
		$xml = simplexml_load_string($xml);
		$this->handleXmlErrors();
		libxml_use_internal_errors($previousState);

		return $xml;
	}

	/**
	 * Vyporada se s chybami pri parsovani xml, pokud narazi na chybu, vyhodi vyjimku.
	 */
	private function handleXmlErrors()
	{
		if (count(libxml_get_errors()))
		{
			$error = NULL;
			foreach (libxml_get_errors() as $e)
			{
				$error = $e;
			}

			libxml_clear_errors();
			libxml_use_internal_errors($previousState);

			// TODO: Vlastni exception class, ktera zobrazi vsechny chyby v XML
			throw new \Exception($error->message, $error->code);
		}
	}

}
