<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Test;

require_once __DIR__ . '/NullProvider.php';

use Webiny\GeoIp\GeoIp;
use Webiny\GeoIp\GeoIpException;

class GeoIpTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $instance = new GeoIp(new NullProvider());

        $this->assertInstanceOf('Webiny\GeoIp\GeoIp', $instance);
    }

    /**
     * @param $query
     * @param $expectedResult
     *
     * @throws \Exception
     * @dataProvider provider
     */
    public function testGetGeoIpLocation($query, $expectedResult)
    {
        $instance = new GeoIp(new NullProvider());

        try {
            $result = $instance->getGeoIpLocation($query);

            if ($result == true) {
                $this->assertInstanceOf('Webiny\GeoIp\Location', $result);
                $this->assertTrue($expectedResult);
            } else {
                $this->assertFalse($expectedResult);
            }
        } catch (GeoIpException $e) {
            $this->assertSame('exception', $expectedResult);
        }
    }

    public function provider()
    {
        return [
            ['2.2.2.2', false],
            ['1.1.1.1', true],
            ['2', 'exception'],
            ['1:0:0:0:0:0:0:1', true],
            ['1:0:0:0:0:0:0:2', false]
        ];
    }


}