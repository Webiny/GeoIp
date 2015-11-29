<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Provider\MaxMindGeoLite2;

use Webiny\Component\Config\ConfigObject;
use Webiny\Component\Entity\Entity;
use Webiny\Component\Mongo\Mongo;
use Webiny\Component\StdLib\StdLibTrait;
use Webiny\GeoIp\Location;
use Webiny\GeoIp\ProviderGeoIpNotFound;
use Webiny\GeoIp\ProviderInterface;

/**
 * GeoIp provider using the FreeGeoIp service (https://freegeoip.net/).
 *
 * @package Webiny\GeoIp\Provider\TelizeApi
 */
class MaxMindGeoLite2 implements ProviderInterface
{
    use StdLibTrait;

    /**
     * Base constructor.
     *
     * @param string $config Path to the MaxMind configuration.
     *
     * @throws \Webiny\Component\StdLib\Exception\Exception
     */
    public function __construct($config)
    {
        $config = \Webiny\Component\Config\Config::getInstance()->yaml($config);

        // bootstrap
        Mongo::setConfig(['Mongo' => $config->get('Mongo', null, true)]);
        Entity::setConfig(['Entity' => $config->get('Entity', null, true)]);
    }

    /**
     * Returns the Location for the provided IPv4 address.
     *
     * @param string $ip4 IPv4 address.
     *
     * @return Location
     * @throws ProviderGeoIpNotFound
     */
    public function getLocationFromIPv4($ip4)
    {
        // get by starting block
        $subNet = $this->str($ip4)->explode('.')->removeLast()->implode('.')->val();
        $ipStart = $subNet . '.0';

        // get by start ip
        $ipLong = ip2long($ip4);
        $cityBlock = CityBlockIp4Entity::findOne([
            'rangeStart' => ip2long($ipStart)
        ]);

        // verify that end ip is within range
        if (!$cityBlock || !($ipLong <= $cityBlock->rangeEnd)) {
            throw new ProviderGeoIpNotFound('GeoIp entry not found');
        }
        


        // get location info
        $locationInfo = LocationEntity::findOne(['geoId' => $cityBlock->geoId]);

        // populate location variable
        $location = new Location();
        $location->setContinent($locationInfo->continentCode, $locationInfo->continentName);
        $location->setCountry($locationInfo->countryCode, $locationInfo->countryName);
        $location->setCityName($locationInfo->cityName);
        $location->setSubdivision1($locationInfo->subdivision1Code, $locationInfo->subdivision1Name);
        $location->setSubdivision2($locationInfo->subdivision2Code, $locationInfo->subdivision2Name);
        $location->setTimeZone($locationInfo->timeZone);

        return $location;
    }

    /**
     * Returns the Location for the provided IPv6 address.
     *
     * @param string $ip6 IPv6 address.
     *
     * @throws ProviderGeoIpNotFound
     * @return Location
     */
    public function getLocationFromIPv6($ip6)
    {
        // create a cidr block
        $ip = $this->str($ip6)->explode(':');
        do {
            $ip->removeLast();
        } while ($ip->count() > 3);

        $subNet = $ip->implode(':')->val();
        $ipStart = floatval(Ipv6Helper::ip2LongV6($subNet . ':8000:0:0:0:0'));

        $cityBlock = CityBlockIp6Entity::findOne([
            'rangeStart' => $ipStart
        ]);

        if (!$cityBlock) {
            $ipStart = floatval(Ipv6Helper::ip2LongV6($subNet . ':0:0:0:0:0'));
            $cityBlock = CityBlockIp6Entity::findOne([
                'rangeStart' => $ipStart
            ]);
        }

        // verify that end ip is within range
        if (!$cityBlock || !(Ipv6Helper::ip2LongV6($ip6) <= $cityBlock->rangeEnd)) {
            throw new ProviderGeoIpNotFound('GeoIp entry not found');
        }

        // get location info
        $locationInfo = LocationEntity::findOne(['geoId' => $cityBlock->geoId]);

        // populate location variable
        $location = new Location();
        $location->setContinent($locationInfo->continentCode, $locationInfo->continentName);
        $location->setCountry($locationInfo->countryCode, $locationInfo->countryName);
        $location->setCityName($locationInfo->cityName);
        $location->setSubdivision1($locationInfo->subdivision1Code, $locationInfo->subdivision1Name);
        $location->setSubdivision2($locationInfo->subdivision2Code, $locationInfo->subdivision2Name);
        $location->setTimeZone($locationInfo->timeZone);

        return $location;
    }
}