<?php
set_time_limit(0);

// composer autoloader
require_once __DIR__ . '/../../../../../vendor/autoload.php';

$configFile = $argv[1];
if(!file_exists($configFile)){
    echo "\n Unable to locate config file: ".$configFile."\n";
    die();
}

$installer = new \Webiny\GeoIp\Provider\MaxMindGeoLite2\Install();
$installer->runInstaller($configFile);