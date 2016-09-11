<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Provider\FreeGeoIp;

use Webiny\GeoIp\Location;
use Webiny\GeoIp\ProviderGeoIpNotFound;
use Webiny\GeoIp\ProviderInterface;

/**
 * GeoIp provider using the FreeGeoIp service (https://freegeoip.net/).
 * Note that this provider only returns country data, city name and timezone.
 *
 * @package Webiny\GeoIp\Provider\TelizeApi
 */
class FreeGeoIp implements ProviderInterface
{
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
        $result = $this->getLocationInfo($ip4);
        if (!$result) {
            throw new ProviderGeoIpNotFound('Unable to find the GeoIp entry for the given ip: ' . $ip4);
        }

        return $result;
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
        $result = $this->getLocationInfo($ip6);
        if (!$result) {
            throw new ProviderGeoIpNotFound('Unable to find the GeoIp entry for the given ip: ' . $ip6);
        }

        return $result;
    }

    /**
     * Does the geo ip lookup.
     *
     * @param string $ip
     *
     * @return bool|Location
     * @throws \Webiny\GeoIp\GeoIpException
     */
    private function getLocationInfo($ip)
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 3, // seconds
            ]
        ]);

        $geoData = @file_get_contents('https://freegeoip.net/json/' . $ip, false, $ctx);

        if ($geoData == false) {
            return false;
        }

        $geoData = @json_decode($geoData, true);
        if ($geoData == false) {
            return false;
        }
        
        // Since setCountry throws an exception if country_code is missing, let's check it here before proceeding
        // This can happen if provider returns empty results - happened when sent a local IP address
        $hasCountryCode = isset($geoData['country_code']) && $geoData['country_code'] && strlen($geoData['country_code']) === 2;
        if (!$hasCountryCode) {
            return false;
        }

        $location = new Location();
        $location->setCountry($geoData['country_code'], $geoData['country_name']);
        $location->setCityName($geoData['city']);
        $location->setTimeZone($geoData['time_zone']);

        if (isset($geoData['region_code'])) {
            $location->setSubdivision1($geoData['region_code'], $geoData['region_name']);
        }


        return $location;
    }

}
