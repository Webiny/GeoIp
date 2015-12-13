<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Test;

use Webiny\GeoIp\Location;
use Webiny\GeoIp\ProviderGeoIpNotFound;
use Webiny\GeoIp\ProviderInterface;

class NullProvider implements ProviderInterface
{

    /**
     * Returns the Location for the provided IPv4 address.
     *
     * @param string $ip4 IPv4 address.
     *
     * @throws ProviderGeoIpNotFound
     * @return Location
     */
    public function getLocationFromIPv4($ip4)
    {
        if ($ip4 == '1.1.1.1') {
            return new Location();
        }

        throw new ProviderGeoIpNotFound;
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
        if ($ip6 == '1:0:0:0:0:0:0:1') {
            return new Location();
        }

        throw new ProviderGeoIpNotFound;
    }
}