<?php

namespace Shipment;

class EbayShipmentFile
{
    protected $handle;
    protected $filename;
    protected $csvtitle;

    public function __construct($country)
    {
        $this->csvtitle = [
            'OrderID',
            'Date',
            'Carrier',
            'TrackingNumber',
            'TransactionID'
        ];

#       $this->filename = 'w:/out/shipping/rakuten_tracking.txt';
        $this->filename = 'E:/BTE/shipping/ebay_tracking.txt';
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function write($data)
    {
        if (!$this->handle) {
            $this->handle = fopen($this->filename, 'w');
            fputcsv($this->handle, $this->csvtitle);
        }

        if (count($data) != count($this->csvtitle)) {
            throw new \Exception('Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data);
    }
}
