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
class CityBlockIp6Entity extends \Webiny\Component\Entity\EntityAbstract
{
    protected static $entityCollection = "GeoIpCityBlockIp6";

    /**
     * This method is called during instantiation to build entity structure
     * @return void
     */
    protected function entityStructure()
    {
        $this->attr('rangeStart')->float()
             ->attr("rangeEnd")->float()
             ->attr("geoId")->integer();
    }
}