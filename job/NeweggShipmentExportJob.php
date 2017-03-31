<?php

include 'classes/Job.php';

class NeweggShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('tracking/export/Base.php');

        $job = $this->getJob("tracking/export/Newegg_Tracking_Exporter.php");

        $job->export();
    }

    protected function getJob($filename)
    {
        include_once($filename);

        $path = pathinfo($filename);
        $class = $path['filename'];

        $job = false;

        if (class_exists($class)) {
            $job = new $class;
        }

        return $job;
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggShipmentExportJob();
$job->run($argv);

