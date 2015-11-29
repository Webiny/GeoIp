<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Provider\MaxMindGeoLite2;

/**
 * Entity for Location records.
 *
 * @package Webiny\GeoIp\Provider\MaxMindGeoLite2
 */
class LocationEntity extends \Webiny\Component\Entity\EntityAbstract
{
    protected static $entityCollection = "GeoIpLocation";

    /**
     * This method is called during instantiation to build entity structure
     * @return void
     */
    protected function entityStructure()
    {
        $this->attr('geoId')->integer()
             ->attr("continentCode")->char()
             ->attr("continentName")->char()
             ->attr("countryCode")->char()
             ->attr("countryName")->char()
             ->attr("subdivision1Name")->char()
            ->attr("subdivision1Code")->char()
             ->attr("subdivision2Name")->char()
            ->attr("subdivision2Code")->char()
             ->attr("cityName")->char()
             ->attr("timeZone")->char();
    }
}