<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Provider\MaxMindGeoLite2;

/**
 * Entity that holds the IPv6 city block entries.
 *
 * @package Webiny\GeoIp\Provider\MaxMindGeoLite2
 */
class CityBlockIp6Entity extends \Webiny\Component\Entity\AbstractEntity
{
    protected static $entityCollection = "GeoIpCityBlockIp6";

    public function __construct()
    {
        parent::__construct();
        $this->attr('rangeStart')->float();
        $this->attr('rangeEnd')->float();
        $this->attr('geoId')->integer();
    }
}