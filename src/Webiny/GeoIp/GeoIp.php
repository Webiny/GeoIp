<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp;

use Webiny\Component\Http\HttpTrait;

/**
 * Main GeoIp class.
 *
 * @package Webiny\GeoIp
 */
class GeoIp
{
    use HttpTrait;

    /**
     * Name of the session variable (used to cache the result).
     */
    const SESSION_KEY = 'webiny_geo_ip';

    /**
     * @var ProviderInterface
     */
    private $provider;


    /**
     * Base constructor.
     *
     * @param ProviderInterface $provider Provider instance.
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Returns the GeoIp Location for the given IP address.
     *
     * @param null|string $ip Address for which to return the location data.
     *                        Note, if you leave it empty, the location will be determined using the current client IP.
     *
     * @return Location
     * @throws GeoIpException
     * @throws \Exception
     */
    public function getGeoIpLocation($ip = null)
    {
        if (empty($ip)) {
            $ip = $this->getCurrentIp();
        }

        // check if we have it in session
        $sessionKey = $this->getSessionKey($ip);
        $geoData = $this->httpSession()->get($sessionKey, false);
        if ($geoData) {
            $location = new Location();
            $location->populate($geoData);

            return $location;
        }

        $protocol = $this->getIpProtocol($ip);

        try {
            if ($protocol == 4) {
                $location = $this->provider->getLocationFromIPv4($ip);
            } else {
                $location = $this->provider->getLocationFromIPv6($ip);
            }

            // save it into session
            $this->httpSession()->save($sessionKey, $location->exportToJson());

            return $location;
        } catch (ProviderGeoIpNotFound $e) {
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Returns the protocol version for the given $ip.
     *
     * @param string $ip Ip address.
     *
     * @return int Protocol version, 4 or 6.
     * @throws GeoIpException
     */
    protected function getIpProtocol($ip)
    {
        // check if v4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 4;
        }

        // check if v6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 6;
        }

        throw new GeoIpException('Unable to determine the IP protocol version for "' . $ip . '".');
    }

    /**
     * Returns the IP address of the client.
     *
     * @return string
     */
    protected function getCurrentIp()
    {
        return $this->httpRequest()->getClientIp();
    }

    private function getSessionKey($ip)
    {
        return self::SESSION_KEY . '_' . md5($ip);
    }
}