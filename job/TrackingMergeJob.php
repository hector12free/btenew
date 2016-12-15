<?php

include 'classes/Job.php';

use Shipment\MasterShipmentFile;
use Shipment\AmazonShipmentFile;

class TrackingMergeJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $drivers = $this->getTrackingDrivers();

        $master   = new MasterShipmentFile();
        $amazonCA = new AmazonShipmentFile('CA');
        $amazonUS = new AmazonShipmentFile('US');

        foreach ($drivers as $driver) {
            $driver->setMasterShipment($master);
            $driver->setAmazonCAshipment($amazonCA);
            $driver->setAmazonUSshipment($amazonUS);
           #$driver->download();
            $driver->merge();
        }

        //$master->compack();
    }

    protected function getTrackingDrivers()
    {
        $drivers = [];

        foreach (glob("tracking/*.php") as $filename) {
            include $filename;

            $path = pathinfo($filename);
            $class = $path['filename'];

            if (class_exists($class)) {
                $driver = new $class;
                $status = $driver->getStatus();
                if ($status > 0) {
                    $drivers[] = $driver;
                }
            }
        }

        return $drivers;
    }
}

include __DIR__ . '/../public/init.php';

$job = new TrackingMergeJob();
$job->run($argv);
