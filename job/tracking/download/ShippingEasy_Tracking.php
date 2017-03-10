<?php

class ShippingEasy_Tracking extends TrackingDownloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/shippingeasy-shipping-report.csv';
        $filename = Filenames::get('shippingeasy.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
