<?php

include 'classes/Job.php';

class eBayOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('order/download/Base.php');

        $job = $this->getJob("order/download/Ebay_Order_Downloader.php");

        $job->download();
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

$job = new eBayOrderDownloadJob();
$job->run($argv);
