<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Provider\MaxMindGeoLite2;

/**
 * Helper functions for IPv6 calculations.
 *
 * @package Webiny\GeoIp\Provider\MaxMindGeoLite2
 */
class Ipv6Helper
{
    /**
     * Calculates the cidr range for the given IPv6 CIDR block.
     *
     * @param string $prefix IPv6 CIDR block.
     *
     * @return array
     */
    public static function calculateIpv6CidrRange($prefix)
    {
        // Split in address and prefix length
        list($firstaddrstr, $prefixlen) = explode('/', $prefix);

        // Parse the address into a binary string
        $firstaddrbin = inet_pton($firstaddrstr);

        // Convert the binary string to a string with hexadecimal characters
        # unpack() can be replaced with bin2hex()
        # unpack() is used for symmetry with pack() below
        $upack = unpack('H*', $firstaddrbin);
        $firstaddrhex = reset($upack);

        // Overwriting first address string to make sure notation is optimal
        $firstaddrstr = inet_ntop($firstaddrbin);

        // Calculate the number of 'flexible' bits
        $flexbits = 128 - $prefixlen;

        // Build the hexadecimal string of the last address
        $lastaddrhex = $firstaddrhex;

        // We start at the end of the string (which is always 32 characters long)
        $pos = 31;
        while ($flexbits > 0) {
            // Get the character at this position
            $orig = substr($lastaddrhex, $pos, 1);

            // Convert it to an integer
            $origval = hexdec($orig);

            // OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
            $newval = $origval | (pow(2, min(4, $flexbits)) - 1);

            // Convert it back to a hexadecimal character
            $new = dechex($newval);

            // And put that character back in the string
            $lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

            // We processed one nibble, move to previous position
            $flexbits -= 4;
            $pos -= 1;
        }

        // Convert the hexadecimal string to a binary string
        # Using pack() here
        # Newer PHP version can use hex2bin()
        $lastaddrbin = pack('H*', $lastaddrhex);

        // And create an IPv6 address from the binary string
        $lastaddrstr = inet_ntop($lastaddrbin);

        return [
            'start' => self::ip2LongV6($firstaddrstr),
            'end'   => self::ip2LongV6($lastaddrstr)
        ];
    }

    /**
     * Converts the given IPv6 address into a numeric representation.
     *
     * @param string $ip IPv6 address to convert.
     *
     * @return string
     */
    public static function ip2LongV6($ip)
    {
        $ip_n = inet_pton($ip);
        $bin = '';
        for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
        }

        if (function_exists('gmp_init')) {
            return gmp_strval(gmp_init($bin, 2), 10);
        } elseif (function_exists('bcadd')) {
            $dec = '0';
            for ($i = 0; $i < strlen($bin); $i++) {
                $dec = bcmul($dec, '2', 0);
                $dec = bcadd($dec, $bin[$i], 0);
            }
            return $dec;
        } else {
            trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
        }
    }
}