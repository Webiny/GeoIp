<?php
/**
 * Webiny Framework (http://www.webiny.com/framework)
 *
 * @copyright Copyright Webiny LTD
 */
namespace Webiny\GeoIp\Provider\MaxMindGeoLite2;

use Webiny\Component\Config\Config;
use Webiny\Component\Entity\Entity;
use Webiny\Component\Mongo\Index\SingleIndex;
use Webiny\Component\Mongo\Mongo;
use Webiny\Component\Mongo\MongoTrait;
use Webiny\Component\StdLib\StdLibTrait;

/**
 * Class that holds the MaxMind GeoLite2 installation steps.
 *
 * @package Webiny\GeoIp\Provider\MaxMindGeoLite2
 */
class Install
{
    use MongoTrait, StdLibTrait;

    /**
     * @var Config
     */
    private $config;
    /**
     * @var string Path where the GeoLite2 database was downloaded.
     */
    private $savePath;
    /**
     * @var string Full path where the GeoLite2 database is located after it was unpacked.
     */
    private $dbFolder;
    /**
     * @var Mongo
     */
    private $mongo;


    /**
     * Runs the installation.
     *
     * @throws \Webiny\Component\StdLib\Exception\Exception
     */
    public function runInstaller($configFile)
    {
        // bootstrap
        $this->config = Config::getInstance()->yaml($configFile);
        Mongo::setConfig(['Mongo' => $this->config->get('Mongo', null, true)]);
        Entity::setConfig(['Entity' => $this->config->get('Entity', null, true)]);

        /**
         * @var $mongo Mongo
         */
        $this->mongo = $this->mongo($this->config->Entity->Database);

        // import process
        $this->downloadDatabase();
        $this->unpackDatabase();
        $this->importLocations();
        //$this->importIp4CityBlock();
        //$this->importIp6CityBlock();

        echo "\nImport has finished.";
        die();
    }

    /**
     * Download the GeoLite2 database.
     */
    private function downloadDatabase()
    {
        // download the GeoLite2 database
        $this->savePath = __DIR__ . '/geoLite2.zip';

        if (file_exists($this->savePath)) {
            unlink($this->savePath);
        }

        echo "\nDownloading ... please wait\n";

        $rh = fopen($this->config->MaxMind->GeoLite2Url, 'rb');
        $wh = fopen($this->savePath, 'w+b');
        if (!$rh || !$wh) {
            echo "\nUnable to save the database.";
            die();
        }

        while (!feof($rh)) {
            if (fwrite($wh, fread($rh, 4096)) === false) {
                echo "\nUnable to save the database.";
                die();
            }
            echo '*';
            flush();
        }

        fclose($rh);
        fclose($wh);
    }

    /**
     * Unpacks the downloaded database.
     */
    private function unpackDatabase()
    {
        echo "\nDownload done ... unpacking contents\n";

        // unzip
        $zip = new \ZipArchive;
        $res = $zip->open($this->savePath);
        if ($res === true) {
            $zip->extractTo(__DIR__);
            $zip->close();
            echo "\nUnpacking done\n";
        } else {
            echo "\nError: unpacking failed\n";
            die();
        }

        // remove the database file
        if (file_exists($this->savePath)) {
            unlink($this->savePath);
        }

        // get the database folder
        $dbFolder = false;
        $h = opendir(__DIR__);
        while (($dir = readdir($h)) !== false) {
            if (strpos(strtolower($dir), 'geolite2-city-csv') !== false) {
                $dbFolder = $dir;
                break;
            }
        }
        if (!$dbFolder) {
            echo "\nUnable to file the folder where we extracted the GeoLite2 database.";
            die();
        }

        $this->dbFolder = dirname($this->savePath) . '/' . $dbFolder . '/';
    }

    /**
     * Import location entries.
     */
    public function importLocations()
    {
        // locations db file
        $locationsFile = $this->dbFolder . 'GeoLite2-City-Locations-' . $this->config->MaxMind->Language . '.csv';

        // start the import
        /**
         * @var $mongo Mongo
         */

        echo "\nImporting locations ... please give it couple of minutes to finish.";

        $handle = fopen($locationsFile, "r");
        fgetcsv($handle, 0, ","); // remove the header row
        while (($ld = fgetcsv($handle, 0, ",")) !== false) {
            $locationEntity = new LocationEntity();
            $locationEntity->geoId = $ld[0];
            $locationEntity->continentCode = strtoupper($ld[2]);
            $locationEntity->continentName = $ld[3];
            $locationEntity->countryCode = strtoupper($ld[4]);
            $locationEntity->countryName = $ld[5];
            $locationEntity->subdivision1Code = $ld[6];
            $locationEntity->subdivision1Name = $ld[7];
            $locationEntity->subdivision2Code = $ld[8];
            $locationEntity->subdivision2Name = $ld[9];
            $locationEntity->cityName = $ld[10];
            $locationEntity->timeZone = $ld[12];
            $locationEntity->save();
        }
        fclose($handle);

        // ensure location index
        $index = new SingleIndex('geoId', 'geoId');
        $this->mongo->createIndex('GeoIpLocation', $index);

        echo "\nLocation import done\n";
    }

    /**
     * Import IPv4 city block entries.
     *
     * @throws \Webiny\Component\StdLib\StdObject\StringObject\StringObjectException
     */
    public function importIp4CityBlock()
    {
        // city blocks db file
        $cbFile = $this->dbFolder . 'GeoLite2-City-Blocks-IPv4.csv';

        // start the import
        echo "\nImporting city IPv4 block ... please give it couple of minutes to finish. (like 10-15min, there is 3M+ records to import)";

        $handle = fopen($cbFile, "r");
        fgetcsv($handle, 0, ","); // remove the header row
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            // calculate ip range block
            $cidr = $this->str($row[0])->explode('/');
            $ip_count = 1 << (32 - $cidr->last()->val());
            $start = ip2long($cidr->first()->val());
            $end = $start + $ip_count;

            // save the record
            $cityBlockEntity = new CityBlockIp4Entity();
            $cityBlockEntity->rangeStart = $start;
            $cityBlockEntity->rangeEnd = $end;
            $cityBlockEntity->geoId = floatval($row[1]);
            $cityBlockEntity->save();
        }
        fclose($handle);

        // ensure range index
        $index = new SingleIndex('ipStart', 'rangeStart');
        $this->mongo->createIndex('GeoIpCityBlockIp4', $index);

        echo "\nCity IPv4 block import done\n";
    }

    /**
     * Import IPv6 entries.
     */
    public function importIp6CityBlock()
    {
        // city blocks db file
        $cbFile = $this->dbFolder . 'GeoLite2-City-Blocks-IPv6.csv';

        // start the import
        echo "\nImporting city IPv6 block ... please give it couple of minutes to finish. (about 5min)";

        $handle = fopen($cbFile, "r");
        fgetcsv($handle, 0, ","); // remove the header row
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            // calculate ip range block
            $cidrRange = Ipv6Helper::calculateIpv6CidrRange($row[0]);

            // save the record
            $cityBlockEntity = new CityBlockIp6Entity();
            $cityBlockEntity->rangeStart = $cidrRange['start'];
            $cityBlockEntity->rangeEnd = $cidrRange['end'];
            $cityBlockEntity->geoId = (int)$row[1];
            $cityBlockEntity->save();
        }
        fclose($handle);

        // ensure range index
        $index = new SingleIndex('ipStart', 'rangeStart');
        $this->mongo->createIndex('GeoIpCityBlockIp6', $index);

        echo "\nCity IPv6 block import done\n";
    }
}