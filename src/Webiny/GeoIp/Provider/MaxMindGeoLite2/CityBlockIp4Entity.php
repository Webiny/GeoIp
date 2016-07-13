<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Provider\MaxMindGeoLite2;

/**
 * Entity that holds the IPv4 city block entries.
 *
 * @package Webiny\GeoIp\Provider\MaxMindGeoLite2
 */
class CityBlockIp4Entity extends \Webiny\Component\Entity\AbstractEntity
{
    protected static $entityCollection = "GeoIpCityBlockIp4";

    public function __construct()
    {
        parent::__construct();
        $this->attr('rangeStart')->integer();
        $this->attr('rangeEnd')->integer();
        $this->attr('geoId')->float();
    }
}