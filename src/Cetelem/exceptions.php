<?php

/**
 * Copyright (c) 2014 Tomas Jacik (tomas.jacik@sunfox.cz)
 */

namespace Sunfox\Cetelem;


/**
 * Vyjimka je volana, kdyz xml vracene ze sluzby Cetelemu nelze parsovat.
 */
class XMLParserException extends \Exception
{
}

/**
 * Vyjimka je volana, kdyz je vraceno ze sluzby Cetelemu XML s chybou.
 */
class XMLResponseException extends \Exception
{
}

/**
 * Vyjimka je volana, kdyz je ze sluzby Cetelemu vracen vypocet uveru s parametry,
 * se kterymi nepocitame (zmenilo se rozhrani Cetelemu).
 */
class CetelemResponseException extends \Exception
{
}
