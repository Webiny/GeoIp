<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp;

/**
 * This class is the result of the geo ip lookup.
 *
 * @package Webiny\GeoIp
 */
class Location
{
    /**
     * @var string
     */
    protected $continentCode; // always upper case | 2 char
    /**
     * @var string
     */
    protected $continentName;
    /**
     * @var string
     */
    protected $countryCode; // always upper case | 2 char
    /**
     * @var string
     */
    protected $countryName;
    /**
     * @var string
     */
    protected $cityName;
    /**
     * @var string
     */
    protected $subdivision1Code;
    /**
     * @var string
     */
    protected $subdivision1Name;
    /**
     * @var string
     */
    protected $subdivision2Code;
    /**
     * @var string
     */
    protected $subdivision2Name;
    /**
     * @var string
     */
    protected $timeZone;


    /**
     * Set continent location info.
     *
     * @param string $continentCode
     * @param string $continentName
     *
     * @throws GeoIpException
     */
    public function setContinent($continentCode, $continentName)
    {
        if (strlen($continentCode) != 2) {
            throw new GeoIpException('Continent code needs to have exactly 2 characters.');
        }

        $this->continentCode = strtoupper($continentCode);
        $this->continentName = $continentName;
    }

    /**
     * Set country location info.
     *
     * @param string $countryCode
     * @param string $countryName
     *
     * @throws GeoIpException
     */
    public function setCountry($countryCode, $countryName)
    {
        if (strlen($countryCode) != 2) {
            throw new GeoIpException('Country code needs to have exactly 2 characters.');
        }

        $this->countryCode = strtoupper($countryCode);
        $this->countryName = $countryName;
    }

    /**
     * Set city name.
     *
     * @param string $cityName
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;
    }

    /**
     * Set subdivision 1 info.
     *
     * @param string $subdivision1Name
     * @param string $subdivision1Name
     */
    public function setSubdivision1($subdivision1Code, $subdivision1Name)
    {
        $this->subdivision1Code = $subdivision1Code;
        $this->subdivision1Name = $subdivision1Name;
    }

    /**
     * Set subdivision 2 info.
     *
     * @param string $subdivision2Code
     * @param string $subdivision2Name
     */
    public function setSubdivision2($subdivision2Code, $subdivision2Name)
    {
        $this->subdivision2Code = $subdivision2Code;
        $this->subdivision2Name = $subdivision2Name;
    }

    /**
     * Set the timezone.
     *
     * @param string $timezone
     */
    public function setTimeZone($timezone)
    {
        $this->timeZone = $timezone;
    }

    /**
     * Get continent code - 2 chars, uppercase.
     *
     * @return string
     */
    public function getContinentCode()
    {
        return $this->continentCode;
    }

    /**
     * Get continent name.
     *
     * @return string
     */
    public function getContinentName()
    {
        return $this->continentName;
    }

    /**
     * Get country code - 2 chars, uppercase.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Get country name.
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Get city name.
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * Get subdivision 1 code - upper case.
     *
     * @return string
     */
    public function getSubdivision1Code()
    {
        return $this->subdivision1Code;
    }

    /**
     * Get subdivision 1 name.
     *
     * @return string
     */
    public function getSubdivision1Name()
    {
        return $this->subdivision1Name;
    }

    /**
     * Get subdivision 2 code - upper case.
     *
     * @return string
     */
    public function getSubdivision2Code()
    {
        return $this->subdivision2Code;
    }

    /**
     * Get subdivision 2 name.
     *
     * @return string
     */
    public function getSubdivision2Name()
    {
        return $this->subdivision2Name;
    }

    /**
     * Get timezone.
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Populate location data from a json string.
     *
     * @param string $jsonData
     */
    public function populate($jsonData)
    {
        $data = json_decode($jsonData, true);

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
    }

    /**
     * Export location data as a json string.
     *
     * @return string
     */
    public function exportToJson()
    {
        $data = [
            // continent
            'continentCode'    => $this->getContinentCode(),
            'continentName'    => $this->getContinentName(),
            // country
            'countryCode'      => $this->getCountryCode(),
            'countryName'      => $this->getCountryName(),
            // city
            'cityName'         => $this->getCityName(),
            // subdivision 1
            'subdivision1Code' => $this->getSubdivision1Code(),
            'subdivision1Name' => $this->getSubdivision1Name(),
            // subdivision 2
            'subdivision2Code' => $this->getSubdivision2Code(),
            'subdivision2Name' => $this->getSubdivision2Name(),
            // timezone
            'timezone'         => $this->getTimeZone()
        ];

        return json_encode($data);
    }
}