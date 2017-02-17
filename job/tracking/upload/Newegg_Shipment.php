<?php

use Toolkit\File;

class Newegg_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = 'w:/out/shipping/newegg_canada_tracking.csv';

        $client = new Marketplace\Newegg\Client('CA');
        $client->uploadTracking($filename);

        File::backup($filename);
    }
}
