<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp;

/**
 * Provider interface defines methods that all GeoIp providers need to implement.
 *
 * Interface ProviderInterface
 * @package Webiny\GeoIp
 */
interface ProviderInterface
{
    /**
     * Returns the Location for the provided IPv4 address.
     *
     * @param string $ip4 IPv4 address.
     *
     * @throws ProviderGeoIpNotFound
     * @return Location
     */
    public function getLocationFromIPv4($ip4);

    /**
     * Returns the Location for the provided IPv6 address.
     *
     * @param string $ip6 IPv6 address.
     *
     * @throws ProviderGeoIpNotFound
     * @return Location
     */
    public function getLocationFromIPv6($ip6);
}