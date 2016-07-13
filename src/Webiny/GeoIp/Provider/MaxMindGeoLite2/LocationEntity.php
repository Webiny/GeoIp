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
class LocationEntity extends \Webiny\Component\Entity\AbstractEntity
{
    protected static $entityCollection = "GeoIpLocation";

    public function __construct()
    {
        parent::__construct();
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