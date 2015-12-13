<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Test;

use Webiny\GeoIp\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetContinent()
    {
        $location = new Location();
        $location->setContinent('EU', 'Europe');

        $this->assertSame('EU', $location->getContinentCode());
        $this->assertSame('Europe', $location->getContinentName());
    }

    public function testSetGetCountry()
    {
        $location = new Location();
        $location->setCountry('GB', 'Great Britain');

        $this->assertSame('GB', $location->getCountryCode());
        $this->assertSame('Great Britain', $location->getCountryName());
    }

    public function testSetGetCity()
    {
        $location = new Location();
        $location->setCityName('London');

        $this->assertSame('London', $location->getCityName());
    }

    public function testSetGetSubdivision1()
    {
        $location = new Location();
        $location->setSubdivision1('CA', 'California');

        $this->assertSame('CA', $location->getSubdivision1Code());
        $this->assertSame('California', $location->getSubdivision1Name());
    }

    public function testSetGetSubdivision2()
    {
        $location = new Location();
        $location->setSubdivision2('foo', 'bar');

        $this->assertSame('foo', $location->getSubdivision2Code());
        $this->assertSame('bar', $location->getSubdivision2Name());
    }

    public function testSetGetTimeZone()
    {
        $location = new Location();
        $location->setTimeZone('Europe/London');

        $this->assertSame('Europe/London', $location->getTimeZone());
    }
}