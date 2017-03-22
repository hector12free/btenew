<?php

include 'classes/Job.php';

class RakutenListingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('listing/download/Base.php');
        include_once('listing/Filenames.php');

        $job = $this->getJob("listing/download/Rakuten_Listing.php");

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

$job = new RakutenListingDownloadJob();
$job->run($argv);
